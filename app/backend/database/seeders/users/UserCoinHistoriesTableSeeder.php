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
    protected const SEEDER_DATA_DEVELOP_LENGTH = 500;

    // 履歴作成日として設定する現在日時からの減算日数
    private const START_DATE_SUB_DAYS = 5;
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
        $targetDate = TimeLibrary::subDays($now, self::START_DATE_SUB_DAYS);
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
            UserCoinHistories::CREATED_AT                 => $targetDate,
            UserCoinHistories::UPDATED_AT                 => $targetDate,
        ];

        // insert用データ
        $data = [];

        // データ数
        $this->count = $this->getSeederDataLengthByEnv();

        // 1~$this->countの数字の配列でforを回す
        foreach (range(1, $this->count) as $i) {
            $row = $template;

            // 価格(200~10000)
            $priceBase = ($i % 10);
            $price = (rand(200, 1000) * (($priceBase === 0) ? 10 : $priceBase));

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

        $userCoinHistriesModel = (new UserCoinHistories());
        $userCoinModel = (new UserCoins());
        $userIds = array_column($data, UserCoinHistories::USER_ID);

        // ユーザーのコイン情報(ユーザーIDで連想配列化)
        $useCoins = $userCoinModel->getAllByUserIds($userIds);
        $useCoins = array_column($useCoins, null, UserCoins::USER_ID);
        DB::beginTransaction();
        try {
            // 購入の場合の購入ステータステーブルの設定は省略する

            $resouces = [];
            foreach ($data as $row) {
                $userId = $row[UserCoinHistories::USER_ID];
                // ユーザーの所持しているコインの更新
                if (empty($useCoins[$userId])) {
                    // 登録されていない場合は新規登録から
                    $userCoinResource = UserCoinsResource::toArrayForCreate(
                        $userId,
                        UserCoins::DEFAULT_COIN_COUNT,
                        UserCoins::DEFAULT_COIN_COUNT,
                        UserCoins::DEFAULT_COIN_COUNT
                    );
                    // 日時の更新
                    $userCoinResource[UserCoins::CREATED_AT] = $targetDate;
                    $userCoinResource[UserCoins::UPDATED_AT] = $targetDate;

                    $userCoinModel->insertByUserId($userId, $userCoinResource);

                    // 再取得
                    $userCoin =$userCoinModel->getRecordByUserId($userId);
                } else {
                    $userCoin = $useCoins[$userId];
                }

                // 各コインいずれかが0より小さくなる場合は更新対象外とする
                $freeCoin = $userCoin[UserCoins::FREE_COINS] + $row[UserCoinHistories::GET_FREE_COINS] - $row[UserCoinHistories::USED_FREE_COINS];
                if ($freeCoin < UserCoins::DEFAULT_COIN_COUNT) {
                    continue;
                }

                $paidCoin = $userCoin[UserCoins::PAID_COINS] + $row[UserCoinHistories::GET_PAID_COINS] - $row[UserCoinHistories::USED_PAID_COINS];
                if ($paidCoin < UserCoins::DEFAULT_COIN_COUNT) {
                    continue;
                }

                $limitedCoin = $userCoin[UserCoins::LIMITED_TIME_COINS]
                    + $row[UserCoinHistories::GET_LIMITED_TIME_COINS]
                    - $row[UserCoinHistories::USED_LIMITED_TIME_COINS]
                    - $row[UserCoinHistories::EXPIRED_LIMITED_TIME_COINS];
                // ランダムで設定した期限切れコイン数が保有コイン数を超過する場合
                if ($limitedCoin < UserCoins::DEFAULT_COIN_COUNT) {
                    continue;
                }

                // ユーザーのコイン情報の更新
                $userCoinResource = UserCoinsResource::toArrayForUpdate(
                    $userId,
                    $freeCoin,
                    $paidCoin,
                    $limitedCoin
                );
                $userCoinModel->updateByUserId($userId, $userCoinResource);

                // コイン履歴の作成
                // $userCoinHistriesModel->insertByUserId($userId, $row); // 個別にinsertする場合
                $resouces[$userId] = $row;
            }
            $userCoinHistriesModel->insertByUserIds($userIds, $resouces);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
