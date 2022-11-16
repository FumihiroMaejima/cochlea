<?php

namespace Database\Seeders\Users;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use App\Exceptions\MyApplicationHttpException;
use App\Http\Resources\Users\UserCoinsResource;
use App\Library\Array\ArrayLibrary;
use App\Library\Message\StatusCodeMessages;
use App\Library\Database\ShardingLibrary;
use App\Library\Random\RandomStringLibrary;
use App\Library\Time\TimeLibrary;
use App\Models\Masters\Coins;
use App\Models\Users\UserCoinHistories;
use App\Models\Users\UserCoins;
use Database\Seeders\BaseSeeder;
use Exception;

class UserCoinHistoriesTableSeeder extends BaseSeeder
{
    protected const SEEDER_DATA_LENGTH = 5;
    protected const SEEDER_DATA_TESTING_LENGTH = 5;
    protected const SEEDER_DATA_DEVELOP_LENGTH = 100;

    // 終了日時として設定する加算日数
    private const END_DATE_ADDITIONAL_DAYS = 5;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->tableName = (new UserCoinHistories())->getTable();

        $now = TimeLibrary::getCurrentDateTime();
        $expiredDate = TimeLibrary::addDays($now, self::END_DATE_ADDITIONAL_DAYS);
        $tmpOrderId = RandomStringLibrary::getRandomShuffleString();

        $template = [
            UserCoinHistories::USER_ID                    => 0,
            UserCoinHistories::TYPE                       => 0,
            UserCoinHistories::GET_FREE_COINS             => 0,
            UserCoinHistories::GET_PAID_COINS             => 0,
            UserCoinHistories::GET_LIMITED_TIME_COINS     => 0,
            UserCoinHistories::USED_FREE_COINS            => 0,
            UserCoinHistories::USED_PAID_COINS            => 0,
            UserCoinHistories::USED_LIMITED_TIME_COINS    => 0,
            UserCoinHistories::EXPIRED_LIMITED_TIME_COINS => 0,
            UserCoinHistories::EXPIRED_AT                 => null,
            UserCoinHistories::OEDER_ID                   => null,
            UserCoinHistories::PRODUCT_ID                 => 0,
            UserCoinHistories::CREATED_AT                 => $now,
            UserCoinHistories::UPDATED_AT                 => $now,
        ];

        // insert用データ
        $data = [];

        // データ数
        $this->count = $this->getSeederDataLengthByEnv();

        // 1~$this->countの数字の配列でforを回す
        foreach (range(1, $this->count) as $i) {
            $row = $template;

            // 価格
            $price = rand(100, 500) * $i;

            // 履歴の種類
            $historyType = rand(
                current(UserCoinHistories::USER_COINS_HISTORY_TYPE_VALUES),
                last(UserCoinHistories::USER_COINS_HISTORY_TYPE_VALUES)
            );

            // コインの形式
            $coinType = rand(Coins::COIN_TYPE_FREE, Coins::COIN_TYPE_LIMITED_TIME);


            $row[UserCoinHistories::USER_ID] = $i;
            $row[UserCoinHistories::TYPE] = $historyType;

            switch ($historyType) {
                case UserCoinHistories::USER_COINS_HISTORY_TYPE_PURCHASED: // 購入(有料)
                    $row[UserCoinHistories::GET_PAID_COINS] = $price;
                    $row[UserCoinHistories::OEDER_ID] = $tmpOrderId . $row[UserCoinHistories::USER_ID];

                    break;
                case UserCoinHistories::USER_COINS_HISTORY_TYPE_GAIN: // 獲得(無料、有料、期間限定含む)
                    if ($coinType === Coins::COIN_TYPE_FREE) {
                        $row[UserCoinHistories::GET_FREE_COINS] = $price;
                    } elseif ($coinType === Coins::COIN_TYPE_PAID) {
                        $row[UserCoinHistories::GET_PAID_COINS] = $price;
                    } else {
                        $row[UserCoinHistories::GET_LIMITED_TIME_COINS] = $price;
                        $row[UserCoinHistories::EXPIRED_AT] = $expiredDate;
                    }

                    break;
                case UserCoinHistories::USER_COINS_HISTORY_TYPE_CONSUME: // 消費
                    if ($coinType === Coins::COIN_TYPE_FREE) {
                        $row[UserCoinHistories::USED_FREE_COINS] = $price;
                    } elseif ($coinType === Coins::COIN_TYPE_PAID) {
                        $row[UserCoinHistories::USED_PAID_COINS] = $price;
                    } else {
                        $row[UserCoinHistories::USED_LIMITED_TIME_COINS] = $price;
                    }
                    $row[UserCoinHistories::PRODUCT_ID] = $price;

                    break;
                case UserCoinHistories::USER_COINS_HISTORY_TYPE_EXPIRED: // 期限切れ
                    $row[UserCoinHistories::EXPIRED_LIMITED_TIME_COINS] = $price;
                    $row[UserCoinHistories::PRODUCT_ID] = $price;
                    break;
                case UserCoinHistories::USER_COINS_HISTORY_TYPE_COMPENSATION: // 補填
                    if ($coinType === Coins::COIN_TYPE_FREE) {
                        $row[UserCoinHistories::GET_FREE_COINS] = $price;
                    } elseif ($coinType === Coins::COIN_TYPE_PAID) {
                        $row[UserCoinHistories::GET_PAID_COINS] = $price;
                    } else {
                        $row[UserCoinHistories::GET_LIMITED_TIME_COINS] = $price;
                        $row[UserCoinHistories::EXPIRED_AT] = $expiredDate;
                    }

                    break;
                default:
                    throw new MyApplicationHttpException(
                        StatusCodeMessages::STATUS_500,
                        'Received Invalid Type.: ' . $historyType,
                        [
                            UserCoinHistories::TYPE => $historyType,
                        ]
                    );
                    break;
            }

            $data[] = $row;
        }

        $model = (new UserCoinHistories());
        $useCoinModel = (new UserCoins());
        DB::beginTransaction();
        try {
            foreach ($data as $row) {
                $userId = $row[UserCoinHistories::USER_ID];
                // ユーザーの所持しているコインの更新
                $userCoin = $this->getUserCoinByUserId($row[UserCoinHistories::USER_ID]);

                if (is_null($userCoin)) {
                    // 登録されていない場合は新規登録
                    $userCoinResource = UserCoinsResource::toArrayForCreate(
                        $userId,
                        0,
                        0,
                        0
                    );
                    $useCoinModel->getQueryBuilder($userId)->insert($userCoinResource);
                }

                // ロックをかけて再取得
                $userCoin = $this->getUserCoinByUserId($userId);

                $freeCoin = $userCoin[UserCoins::FREE_COINS] + $row[UserCoinHistories::GET_FREE_COINS] +  $row[UserCoinHistories::USED_FREE_COINS];
                $paidCoin = $userCoin[UserCoins::PAID_COINS] + $row[UserCoinHistories::GET_PAID_COINS] +  $row[UserCoinHistories::USED_PAID_COINS];

                // ランダムで設定した期限切れコイン数が保有コイン数を超過する場合
                if ($userCoin[UserCoins::LIMITED_TIME_COINS] < $row[UserCoinHistories::EXPIRED_LIMITED_TIME_COINS]) {
                    $limitedCoin = 0;
                } else {
                    $limitedCoin = $userCoin[UserCoins::LIMITED_TIME_COINS]
                        + $row[UserCoinHistories::GET_LIMITED_TIME_COINS]
                        + $row[UserCoinHistories::USED_LIMITED_TIME_COINS]
                        - $row[UserCoinHistories::EXPIRED_LIMITED_TIME_COINS];
                }

                // ユーザーのコイン情報の更新
                $userCoinResource = UserCoinsResource::toArrayForUpdate(
                    $userId,
                    $freeCoin,
                    $paidCoin,
                    $limitedCoin
                );
                $useCoinModel->getQueryBuilder($userId)
                    ->where(UserCoins::USER_ID, '=', $userId)
                    ->update($userCoinResource);

                // コイン履歴の更新
                $model->getQueryBuilder($row[UserCoinHistories::USER_ID])->insert($row);
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * get user coins by user id.
     *
     * @param int $userId user id
     * @param bool $isLock exec lock For Update
     * @return array|null
     */
    private function getUserCoinByUserId(int $userId, bool $isLock = false): array|null
    {
        $userCoin = (new UserCoins())
            ->getQueryBuilder($userId)
            ->where(UserCoins::USER_ID, '=', $userId)
            ->get();

        if ($userCoin->count() === 0) {
            return null;
        }

        // 複数チェックはrepository側で実施済み
        return ArrayLibrary::toArray(ArrayLibrary::getFirst($userCoin->toArray()));
        ;
    }
}
