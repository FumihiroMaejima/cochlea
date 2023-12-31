<?php

declare(strict_types=1);

namespace App\Imports\Masters\ServiceTerms;

use App\Models\Masters\Events;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Http\UploadedFile;

class ServiceTermsImport implements ToModel
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
        $this->resource = $this->toArray($file, null, \Maatwebsite\Excel\Excel::CSV);
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
