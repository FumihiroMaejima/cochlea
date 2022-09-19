<?php

namespace App\Exports\Masters\Informations;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Collection;
use App\Models\Masters\Informations;

class InformationsBulkInsertTemplateExport implements FromCollection, WithHeadings, WithTitle, WithMapping
{
    use Exportable;

    private Collection $resource;

    /**
     * @param \Illuminate\Support\Collection $resource
     * @return void
     */
    public function __construct(Collection $collection)
    {
        $this->resource = $collection;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->resource;
    }

    /**
     * setting file headers
     * @return array
     */
    public function headings(): array
    {
        return [
            'お知らせ名',
            '種類',
            '詳細',
            '公開開始日時',
            '公開終了日時',
        ];
    }

    /**
     * setting sheet title (Excel only)
     * @return string
     */
    public function title(): string
    {
        return '敵キャラクター作成テンプレート';
    }

    /**
     * 1行あたりのデータの設定を行う
     * @return string
     */
    public function map($item): array
    {
        // return $data;
        return [
            'name'     => $item->{Informations::NAME},
            'type'     => $item->{Informations::TYPE},
            'detail'   => $item->{Informations::DETAIL},
            'start_at' => $item->{Informations::START_AT},
            'end_at'   => $item->{Informations::END_AT},
        ];
    }
}
