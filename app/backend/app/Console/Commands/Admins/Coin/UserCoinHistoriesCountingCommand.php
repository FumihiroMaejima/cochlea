<?php

namespace App\Console\Commands\Admins\Coin;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use App\Library\Database\ShardingProxyLibrary;
use App\Library\File\CsvLibrary;
use App\Library\Time\TimeLibrary;
use App\Models\Users\UserCoinHistories;

class UserCoinHistoriesCountingCommand extends Command
{
    /**
     * The name and signature of the console command.(コンソールコマンドの名前と使い方)
     *
     * @var string
     */
    protected $signature = 'admins:counting-user-coin-histories'; // if require parameter 'debug:test {param}';

    /**
     * The console command description.(コンソールコマンドの説明)
     *
     * @var string
     */
    protected $description = 'countig user coin histories record in 1 mounth';

    private const CSV_FILE_HEADERS = [
        'プロダクトID',
        '合計無料コイン利用額',
        '合計有料コイン利用額',
        '合計期限付きコイン利用額',
    ];

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
        $currentDateTime = TimeLibrary::getCurrentDateTime();
        $timestamp = TimeLibrary::strToTimeStamp($currentDateTime);
        $YearMonth = TimeLibrary::timeStampToDate($timestamp, TimeLibrary::DEFAULT_DATE_TIME_FORMAT_YEAR_MONTH_ONLY);
        echo $currentDateTime . "\n";
        $records = self::getConsumedUserCoinHistories();
        $groupingRecords = self::groupingUserCoinHistories($records);
        CsvLibrary::createFile(
            $groupingRecords,
            self::CSV_FILE_HEADERS,
            "UserCoinHistoriesByProductId_$YearMonth" . "_$timestamp.csv"
        );
        // echo var_dump($groupingRecords);
    }

    /**
     * get consumed user coin histories
     *
     * @return array
     */
    private static function getConsumedUserCoinHistories(): array
    {
        $timestamp = TimeLibrary::getCurrentDateTimeTimeStamp();
        $startAt = TimeLibrary::startDayOfMonth($timestamp, TimeLibrary::DATE_TIME_FORMAT_START_DATE);
        $endAt = TimeLibrary::lastDayOfMonth($timestamp, TimeLibrary::DATE_TIME_FORMAT_END_DATE);
        return ShardingProxyLibrary::select(
            (new UserCoinHistories())->getTable(),
            equals: [UserCoinHistories::TYPE => UserCoinHistories::USER_COINS_HISTORY_TYPE_CONSUME],
            betweens: [UserCoinHistories::CREATED_AT => [$startAt, $endAt]]
        );
    }

    /**
     * grouping user coin histories
     *
     * @param array $records
     * @return array
     */
    private static function groupingUserCoinHistories(array $records): array
    {
        $response = [];
        foreach ($records as $record) {
            $productId = $record[UserCoinHistories::PRODUCT_ID];
            if (!isset($response[$productId])) {
                $response[$productId] = [
                    UserCoinHistories::PRODUCT_ID => $productId,
                    UserCoinHistories::USED_FREE_COINS => 0,
                    UserCoinHistories::USED_PAID_COINS => 0,
                    UserCoinHistories::USED_LIMITED_TIME_COINS => 0,
                ];
            }
            $tmpResponse = $response[$productId];

            $tmpResponse[UserCoinHistories::USED_FREE_COINS] += $record[UserCoinHistories::USED_FREE_COINS];
            $tmpResponse[UserCoinHistories::USED_PAID_COINS] += $record[UserCoinHistories::USED_PAID_COINS];
            $tmpResponse[UserCoinHistories::USED_LIMITED_TIME_COINS] += $record[UserCoinHistories::USED_LIMITED_TIME_COINS];
            $response[$productId] = $tmpResponse;
        }
        return $response ;
    }
}
