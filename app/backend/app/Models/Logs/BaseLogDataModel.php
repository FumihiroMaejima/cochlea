<?php

namespace App\Models\Logs;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class BaseLogDataModel extends Model
{
    /**
     * get connection name by node number.
     *
     * @param int $userId user id.
     * @return string
     */
    public static function setConnectionName(): string
    {
        return Config::get('myapp.database.logs.baseConnectionName');
    }
}
