<?php

declare(strict_types=1);

namespace App\Models\Logs;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use App\Library\Database\LogTablesLibrary;
use Illuminate\Database\Query\Builder;

class BaseLogDataModel extends Model
{
    /**
     * get log databse connection name.
     *
     * @return string
     */
    public static function getLogDatabaseConnection(): string
    {
        return LogTablesLibrary::getLogDatabaseConnection();
    }

    /**
     * get query builder by user id.
     *
     * @return Builder
     */
    public function getQueryBuilder(): Builder
    {
        return DB::connection(self::getLogDatabaseConnection())
            ->table($this->getTable());
    }
}
