<?php

declare(strict_types=1);

namespace App\Imports\Masters\Admins;

use App\Models\Masters\Admins;
use Maatwebsite\Excel\Concerns\ToModel;

class AdminsImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Admins([
            //
        ]);
    }
}
