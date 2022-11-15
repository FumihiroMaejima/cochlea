<?php

namespace Database\Seeders\Users;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use App\Exceptions\MyApplicationHttpException;
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
                    } else if ($coinType === Coins::COIN_TYPE_PAID) {
                        $row[UserCoinHistories::GET_PAID_COINS] = $price;
                    } else {
                        $row[UserCoinHistories::GET_LIMITED_TIME_COINS] = $price;
                        $row[UserCoinHistories::EXPIRED_AT] = $expiredDate;
                    }

                    break;
                case UserCoinHistories::USER_COINS_HISTORY_TYPE_CONSUME: // 消費
                    if ($coinType === Coins::COIN_TYPE_FREE) {
                        $row[UserCoinHistories::USED_FREE_COINS] = $price;
                    } else if ($coinType === Coins::COIN_TYPE_PAID) {
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
                    } else if ($coinType === Coins::COIN_TYPE_PAID) {
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
        DB::beginTransaction();
        try {
            foreach ($data as $row) {
                $model->getQueryBuilder($row[UserCoinHistories::USER_ID])->insert($data);
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
