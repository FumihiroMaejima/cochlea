<?php

namespace App\Models\Logs;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class BaseLogDataModel extends Model
{
    /**
     * get log databse connection name.
     *
     * @return string
     */
    public static function getLogDatabaseConnection(): string
    {
        return Config::get('myapp.database.logs.baseConnectionName');
    }
}
