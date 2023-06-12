<?php

namespace App\Services\Users;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Exceptions\MyApplicationHttpException;
use App\Library\Message\StatusCodeMessages;
use App\Http\Resources\Users\UsersAuthCodeResource;
use App\Http\Resources\Users\UsersResource;
use App\Repositories\Users\UserCoinHistories\UserCoinHistoriesRepositoryInterface;
use App\Repositories\Users\Users\UsersRepositoryInterface;
use App\Library\Array\ArrayLibrary;
use App\Library\Cache\CacheLibrary;
use App\Library\Auth\AuthCodeLibrary;
use App\Library\Random\RandomStringLibrary;
use App\Library\Time\TimeLibrary;
use App\Models\User;
use App\Models\Users\UserAuthCodes;
use App\Repositories\Users\UserAuthCodes\UserAuthCodesRepositoryInterface;
use App\Services\Admins\Notifications\AuthCodeNotificationService;
use Exception;

class UserAuthService
{
    // cache keys
    private const CACHE_KEY_USER_COIN_COLLECTION_LIST = 'user_coin_collection_list';

    protected UserAuthCodesRepositoryInterface $userAuthCodeRepository;
    protected UsersRepositoryInterface $usersRepository;
    protected UserCoinHistoriesRepositoryInterface $userCoinHistoriesRepositoryInterface;

    /**
     * create instance
     *
     * @param UserAuthCodesRepositoryInterface $userAuthCodeRepository
     * @param UsersRepositoryInterface $userAuthCodeRepository
     * @return void
     */
    public function __construct(
        UserAuthCodesRepositoryInterface $userAuthCodeRepository,
        UsersRepositoryInterface $usersRepository
    ) {
        $this->userAuthCodeRepository = $userAuthCodeRepository;
        $this->usersRepository = $usersRepository;
    }

    /**
     * register user & send auth code.
     *
     * @param string $email email
     * @return JsonResponse
     */
    public function registUserByEmailAndSendAuthCode(string $email): JsonResponse {

        $existUser = $this->usersRepository->getByEmail($email);
        if (!is_null($existUser)) {
            return response()->json(
                [
                    'code' => 200,
                    'message' => 'Success',
                    'data' => [
                        'userId' => $existUser[User::ID],
                    ],
                ]
            );
        }
        $timeStamp = TimeLibrary::strToTimeStamp(TimeLibrary::getCurrentDateTime());
        $token = RandomStringLibrary::getByHashRandomString(RandomStringLibrary::RANDOM_STRING_LENGTH_24);
        $resource = UsersResource::toArrayForCreate($timeStamp,  $email, $token);

        DB::beginTransaction();
        try {
            // ユーザーの登録
            $userId = (new User())->insertUserAndGetId($resource);

            // 6文字のランダム文字列
            $code = RandomStringLibrary::getRandomShuffleInteger(6);
            $expiredAt = TimeLibrary::timeStampToDate($timeStamp + TimeLibrary::HALF_MINUTE_TIME_SECOND_VALUE);

            $authCodeResource = UsersAuthCodeResource::toArrayForCreate(
                $userId,
                UserAuthCodes::TYPE_REGISTER,
                $code,
                0,
                0,
                $expiredAt
            );

            $this->userAuthCodeRepository->create($userId, $authCodeResource);

            // メール送信
            (new AuthCodeNotificationService($email))->send((string)$code, $expiredAt);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_401,
                '認証コード生成処理に失敗しました。' . $e->getMessage(),
                [],
                false
            );
        }

        return response()->json(
            [
                'code' => 200,
                'message' => 'Success',
                'data' => [
                    'userId' => $userId,
                ],
            ]
        );
    }

    /**
     * validate auth code
     *
     * @param int $userId  user id
     * @param int $authCode auth code
     * @return JsonResponse
     */
    public function validateUserAuthCode(int $userId , int $authCode): JsonResponse {

        $existUser = $this->usersRepository->getByUserId($userId);
        if (is_null($existUser)) {
            throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_401,
                'ユーザー情報が存在しません。',
                ['userId' => $userId],
                false
            );
        }

        DB::beginTransaction();
        try {
            $userAuthCodeList = UserAuthCodes::sortByCreatedAt(
                $this->userAuthCodeRepository->getListByUserId($userId)
            );
            if (empty($userAuthCodeList)) {
                throw new MyApplicationHttpException(
                    StatusCodeMessages::STATUS_401,
                    '認証コード情報が存在しません。',
                    ['userId' => $userId],
                    false
                );
            }
            // 最新の認証コード
            $userAuthCode = current($userAuthCodeList);

            // 認証コードの検証
            $isEnable = AuthCodeLibrary::validateAuthCode($userId, $authCode, $userAuthCode);
            $isUsed = $isEnable ? 1 : 0;

            // 認証コード情報の更新
            $authCodeResource = UsersAuthCodeResource::toArrayForUpdate(
                $userId,
                UserAuthCodes::TYPE_REGISTER,
                $userAuthCode[UserAuthCodes::CODE],
                $userAuthCode[UserAuthCodes::COUNT] + 1,
                $isUsed
            );

            $this->userAuthCodeRepository->update($userId, $authCode, $authCodeResource);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_401,
                '認証コードの検証処理に失敗しました。' . $e->getMessage(),
                [],
                false,
                $e->getPrevious()
            );
        }

        if (!$isEnable) {
            throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_401,
                '認証コードの検証処理に失敗しました。 :認証コード不正',
                ['code' => $authCode],
                false
            );
        }

        return response()->json(
            [
                'code' => 200,
                'message' => 'Success',
                'data' => [],
            ]
        );
    }
}
