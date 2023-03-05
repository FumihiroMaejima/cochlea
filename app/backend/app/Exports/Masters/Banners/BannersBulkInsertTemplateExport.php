<?php

namespace App\Exports\Masters\Banners;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Collection;
use App\Models\Masters\Banners;

class BannersBulkInsertTemplateExport implements FromCollection, WithHeadings, WithTitle, WithMapping
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
            'バナー名',
            '詳細',
            '設置場所',
            'PC版height',
            'PC版width',
            'SP版height',
            'SP版width',
            '公開開始日時',
            '公開終了日時',
            'URL',
        ];
    }

    /**
     * setting sheet title (Excel only)
     * @return string
     */
    public function title(): string
    {
        return 'コイン情報作成テンプレート';
    }

    /**
     * 1行あたりのデータの設定を行う
     * @return string
     */
    public function map($item): array
    {
        // return $data;
        return [
            'name'      => $item->{Banners::NAME},
            'detail'    => $item->{Banners::DETAIL},
            'location'  => $item->{Banners::LOCATION},
            'pc_height' => $item->{Banners::PC_HEIGHT},
            'pc_width'  => $item->{Banners::PC_WIDTH},
            'sp_height' => $item->{Banners::SP_HEIGHT},
            'sp_width'  => $item->{Banners::SP_WIDTH},
            'start_at'  => $item->{Banners::START_AT},
            'end_at'    => $item->{Banners::END_AT},
            'url'       => $item->{Banners::URL},
        ];
    }
}
