<?php

namespace App\Exports\Masters\HomeContents;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Collection;
use App\Models\Masters\HomeContentsGroups;

class HomeContentsGroupsExport implements FromCollection, WithHeadings, WithTitle, WithMapping
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
            '名前',
            '順番',
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
        return 'ホームコンテンツ検索結果';
    }

    /**
     * 1行あたりのデータの設定を行う
     * @return string
     */
    public function map($item): array
    {
        // return $data;
        return [
            'name'     => $item->{HomeContentsGroups::NAME},
            'order'    => $item->{HomeContentsGroups::ORDER},
            'start_at' => $item->{HomeContentsGroups::START_AT},
            'end_at'   => $item->{HomeContentsGroups::END_AT},
        ];
    }
}
