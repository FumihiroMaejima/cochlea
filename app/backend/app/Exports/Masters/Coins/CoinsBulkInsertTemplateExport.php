<?php

declare(strict_types=1);

namespace App\Exports\Masters\Coins;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Collection;
use App\Http\Requests\Admin\Coins\CoinBaseRequest;
use App\Models\Masters\Coins;

class CoinsBulkInsertTemplateExport implements FromCollection, WithHeadings, WithTitle, WithMapping
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
            'コイン名',
            '詳細',
            '購入価格',
            'アプリケーション内コスト',
            '公開開始日時',
            '公開終了日時',
            '画像',
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
            'name'   => $item->{Coins::NAME},
            'detail' => $item->{Coins::DETAIL},
            'price'  => $item->{Coins::PRICE},
            'cost'   => $item->{Coins::COST},
            'start_at' => $item->{Coins::START_AT},
            'end_at'   => $item->{Coins::END_AT},
            'image'    => $item->{Coins::IMAGE},
        ];
    }
}
