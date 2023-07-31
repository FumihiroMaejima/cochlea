<?php

namespace App\Console\Commands\Debug\Seeder;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Database\Seeders\Users\UserCoinHistoriesTableSeeder;
use App\Exceptions\MyApplicationHttpException;
use App\Http\Resources\Users\UserCoinsResource;
use App\Library\Array\ArrayLibrary;
use App\Library\Message\StatusCodeMessages;
use App\Library\Database\ShardingLibrary;
use App\Library\Random\RandomStringLibrary;
use App\Library\Time\TimeLibrary;
use App\Library\String\UuidLibrary;
use App\Models\Masters\Coins;
use App\Models\Users\UserCoinHistories;
use App\Models\Users\UserCoins;
use Database\Seeders\BaseSeeder;
use Exception;

class UserCoinHistoriesExpiredSeedCommand extends Command
{
    /**
     * The name and signature of the console command.(コンソールコマンドの名前と使い方)
     *
     * @var string
     */
    protected $signature = 'debug:seed-user-coin-histories-expired {date}'; // if require parameter 'debug:test {param}';

    /**
     * The console command description.(コンソールコマンドの説明)
     *
     * @var string
     */
    protected $description = 'debug insert user coin histories expired';

    /**
     * DebugTestCommandインスタンスの生成
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.(コマンドの実行)
     *
     * @return void
     */
    public function handle(): void
    {
        $date = $this->argument('date');
        echo TimeLibrary::getCurrentDateTime() . "\n";
        echo 'target Date is ' . $date . "\n";
        // 入力チェック
        if (!TimeLibrary::checkDateFormatByHyphen($date)) {
            echo 'Invalid Date Format: ' . $date . "\n";
            return;
        }
        $this->createResource($date);
    }

    /**
     * Run the database seeds.
     *
     * @param string $date
     * @return void
     */
    public function createResource(string $date)
    {
        $now = TimeLibrary::getCurrentDateTime();
        $timestamp =  TimeLibrary::strToTimeStamp($now);

        $template = [
            UserCoinHistories::USER_ID                    => 0,
            UserCoinHistories::UUID                       => '',
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
            UserCoinHistories::CREATED_AT                 => $now, // パーティション設定の都合上、指定しない
            UserCoinHistories::UPDATED_AT                 => $now,
        ];

        // insert用データ
        $data = [];

        // $expiredAt ='2023-08-01 23:59:59';
        $expiredAt ="$date 23:59:59";

        foreach (ShardingLibrary::getShardingSetting() as $node => $shardIds) {
            $connection = ShardingLibrary::getConnectionByNodeNumber($node);
            foreach ($shardIds as $shardId) {
                $records = (new UserCoinHistories())->getAllByConnectionAndShardIdAndGainAndExpireAt(
                    $connection,
                    $shardId,
                    $expiredAt
                );
                if (empty($records)) {
                    continue;
                }

                // 期限切れ設定を追加
                foreach ($records as $record) {
                    $row = $template;
                    $row[UserCoinHistories::USER_ID] =  $record[UserCoinHistories::USER_ID];
                    $row[UserCoinHistories::UUID] = UuidLibrary::uuidVersion4();
                    $row[UserCoinHistories::TYPE] = UserCoinHistories::USER_COINS_HISTORY_TYPE_EXPIRED;
                    $row[UserCoinHistories::EXPIRED_LIMITED_TIME_COINS] = $record[UserCoinHistories::GET_LIMITED_TIME_COINS];
                    $row[UserCoinHistories::PRODUCT_ID] = $record[UserCoinHistories::PRODUCT_ID];
                    $row[UserCoinHistories::EXPIRED_AT] = $expiredAt;
                    $data[] = $row;
                }
            }
        }

        $userCoinHistriesModel = (new UserCoinHistories());
        $userCoinModel = (new UserCoins());
        $userIds = array_unique(array_column($data, UserCoinHistories::USER_ID));

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
                    $userCoinResource[UserCoins::CREATED_AT] = $now;
                    $userCoinResource[UserCoins::UPDATED_AT] = $now;

                    $userCoinModel->insertByUserId($userId, $userCoinResource);

                    // 再取得
                    $userCoin = $userCoinModel->getRecordByUserId($userId);
                } else {
                    $userCoin = $useCoins[$userId];
                }

                // 無料、有料は増減無し
                $freeCoin = $userCoin[UserCoins::FREE_COINS];
                $paidCoin = $userCoin[UserCoins::PAID_COINS];

                // 期限切れの分減算する
                $limitedCoin = $userCoin[UserCoins::LIMITED_TIME_COINS]
                    // + $row[UserCoinHistories::GET_LIMITED_TIME_COINS]
                    // - $row[UserCoinHistories::USED_LIMITED_TIME_COINS]
                    - $row[UserCoinHistories::EXPIRED_LIMITED_TIME_COINS];
                // 期限切れコイン数が保有コイン数を超過する場合
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

                // primary keyを考慮して作成日時を加算する
                if (!empty($resouces[$userId])) {
                    $row[UserCoinHistories::CREATED_AT] =
                    TimeLibrary::timeStampToDate($timestamp + count($resouces[$userId]));
                }

                $resouces[$userId][] = $row;

                // 削除対象レコードが複数あるケースを考慮してユーザーレコード変数を更新する
                $userCoin[UserCoins::LIMITED_TIME_COINS] = $limitedCoin;
                $useCoins[$userId] = $userCoin;
            }
            // コイン履歴の作成
            $userCoinHistriesModel->insertByUserIdsForMultiUserRecords($userIds, $resouces);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
