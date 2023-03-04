<?php

namespace App\Http\Controllers\Admins;

use App\Exceptions\MyApplicationHttpException;
use App\Http\Controllers\Controller;
use App\Library\Message\StatusCodeMessages;
use App\Library\Random\RandomStringLibrary;
use App\Library\Session\SessionLibrary;
use App\Library\Time\TimeLibrary;
use App\Models\Masters\Admins;
use App\Models\Masters\OAuthUsers;
use App\Repositories\Admins\AdminsRoles\AdminsRolesRepositoryInterface;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\SocialiteManager;
use Laravel\Socialite\Two\GoogleProvider;
use Exception;

class SocialLoginController extends Controller
{
    // login response
    private const LOGIN_RESEPONSE_KEY_ACCESS_TOKEN = 'access_token';
    private const LOGIN_RESEPONSE_KEY_TOKEN_TYPE = 'token_type';
    private const LOGIN_RESEPONSE_KEY_EXPIRES_IN = 'expires_in';
    private const LOGIN_RESEPONSE_KEY_USER = 'user';

    // admin resource key
    private const ADMIN_RESOURCE_KEY_ID = 'id';
    private const ADMIN_RESOURCE_KEY_NAME = 'name';
    private const ADMIN_RESOURCE_KEY_AUTHORITY = 'authority';

    // token prefix
    private const TOKEN_PREFIX = 'bearer';

    private const SESSION_TTL = 60; // 60秒

    /**
     * Githubへのリダイレクト処理
     *
     * @return \Illuminate\Http\JsonResponse
     * @retutn \Symfony\Component\HttpFoundation\RedirectResponse|\Illuminate\Http\RedirectResponse
     */
    public function redirectToGitHub(): \Symfony\Component\HttpFoundation\RedirectResponse|\Illuminate\Http\RedirectResponse
    {
        $provider = Socialite::driver('github');

        return $provider->redirect();
    }

    /**
     * 認証後のコールバック処理
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function callBackOfGitHub(Request $request)
    {
        // バリデーションチェック
        $validator = Validator::make(
            $request->all(),
            [
                'code' => ['required','string'],
                'state' => ['required','string'],
            ]
        );

        if ($validator->fails()) {
            throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_422,
                'Validataion Error.',
                $validator->errors()->toArray()
            );
        }

        try {
            $user = Socialite::driver('github')->user();

            $userId = $user->getId();
            $oAuthUser = (new OAuthUsers())->getRecordByGitHubUserId($userId);

            if (is_null($oAuthUser)) {
                $token = RandomStringLibrary::getByHashRandomString(RandomStringLibrary::RANDOM_STRING_LENGTH_24);
                $timeStamp = TimeLibrary::strToTimeStamp(TimeLibrary::getCurrentDateTime());
                (new OAuthUsers())->insertByUserId([
                    OAuthUsers::NAME => $timeStamp,
                    OAuthUsers::GIT_HUB_ID => $userId,
                    OAuthUsers::GIT_HUB_TOKEN => $token . $timeStamp,
                    'code' => $request->code,
                    'state' => $request->state,
                ]);
            }
        } catch (Exception $e) {
            throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_401,
                'リダイレクト後のコールバック処理に失敗しました。 previous:' . $e->getMessage(),
                [],
                false
            );
        }

        // TODO セッションの付与と登録と返却

        if (!is_null($oAuthUser)) {
            return response()->json([
                'message' => 'Success',
                'data' => [
                    OAuthUsers::NAME => $oAuthUser[OAuthUsers::NAME],
                    OAuthUsers::GIT_HUB_TOKEN => $oAuthUser[OAuthUsers::GIT_HUB_TOKEN],
                ]
            ], 200);
        }


        return response()->json([
            'message' => 'created',
            'data' => []
        ], 201);
    }
}
