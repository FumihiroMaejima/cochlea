<?php

namespace App\Imports\Admins;

use App\Models\Admins;
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
