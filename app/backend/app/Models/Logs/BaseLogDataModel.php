<?php

namespace App\Models\Logs;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use App\Library\Database\LogTablesLibrary;

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
}
