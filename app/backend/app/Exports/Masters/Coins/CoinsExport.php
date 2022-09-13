<?php

namespace App\Exports\Masters\Coins;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Collection;
use App\Http\Requests\Admin\Coins\CoinBaseRequest;
use App\Models\Masters\Coins;

class CoinsExport implements FromCollection, WithHeadings, WithTitle, WithMapping
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
            'id',
            'コイン名',
            '詳細',
            '購入価格',
            'アプリケーション内コスト'
        ];
    }

    /**
     * setting sheet title (Excel only)
     * @return string
     */
    public function title(): string
    {
        return 'コイン検索結果';
    }

    /**
     * 1行あたりのデータの設定を行う
     * @return string
     */
    public function map($item): array
    {
        // return $data;
        return [
            'id'     => $item->{CoinBaseRequest::KEY_ID},
            'name'   => $item->{CoinBaseRequest::KEY_NAME},
            'detail' => $item->{CoinBaseRequest::KEY_DETAIL},
            'price'  => $item->{CoinBaseRequest::KEY_PRICE},
            'cost'   => $item->{CoinBaseRequest::KEY_COST},
        ];
    }
}
