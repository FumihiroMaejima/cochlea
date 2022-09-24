<?php

namespace App\Imports\Masters\Informations;

use App\Models\Masters\Informations;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Http\UploadedFile;

class InformationsImport implements ToModel
{
    use Importable;
    private array $resource;

    /**
     * create constructer.
     * @param \Illuminate\Http\UploadedFile $file
     * @return void
     */
    public function __construct(UploadedFile $file)
    {
        $this->resource = $this->toArray($file, null, \Maatwebsite\Excel\Excel::XLSX);
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return $this->resource;
        /* return new Coins([
            //
        ]); */
    }
}
