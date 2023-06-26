<?php

namespace App\Library\Performance;

class PerformanceLibrary
{
    // メモリ使用量の例
    // App\Library\Performance\MemoryLibrary::getIntValueListUsage(100); // "8.05 KB"
    // App\Library\Performance\MemoryLibrary::getIntValueListUsage(1000); // "36.05 KB"
    // App\Library\Performance\MemoryLibrary::getIntValueListUsage(10000); // "516.05 KB"
    // App\Library\Performance\MemoryLibrary::getIntValueListUsage(100000); // "4 MB"
    // App\Library\Performance\MemoryLibrary::getIntValueListUsage(1000000); // "32 MB"
    // App\Library\Performance\MemoryLibrary::getIntValueListUsage(10000000); // "512 MB"

    public const MEMORY_UNIT = ['B','KB','MB','GB','TB','PB'];
    public const BASE_BIT = 1024;

    public const ONE_DAY_HOURS = 24;
    public const ONE_HOUR_SECONDS = 3600;

    /**
     * get daily active user
     *
     * @param int $activeUserCount
     * @param int $everyDayActiveUserRate (x%)
     * @return float
     */
    public static function getDailyActiveUser(int $activeUserCount, int $everyDayActiveUserRate): float
    {
        return floor($activeUserCount * ($everyDayActiveUserRate / 100));
    }

    /**
     * get query count per second
     *
     * @param int $queryCount
     * @return float
     */
    public static function getQueryPerSecond(int $queryCount): float
    {
        return floor($queryCount / self::ONE_DAY_HOURS / self::ONE_HOUR_SECONDS);
    }
}
