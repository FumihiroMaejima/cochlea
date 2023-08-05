<?php

namespace App\Exports\Masters\ServiceTerms;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Collection;
use App\Models\Masters\ServiceTerms;

class ServiceTermsBulkInsertTemplateExport implements FromCollection, WithHeadings, WithTitle, WithMapping
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
            'バージョン',
            '利用規約',
            'プライバシーポリシー',
            'メモ',
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
        return '利用規約・プライバシーポリシー作成テンプレート';
    }

    /**
     * 1行あたりのデータの設定を行う
     * @return string
     */
    public function map($item): array
    {
        // return $data;
        return [
            'version'        => $item->{ServiceTerms::VERSION},
            'terms'          => $item->{ServiceTerms::TERMS},
            'privacy_poricy' => $item->{ServiceTerms::PRIVACY_POLICY},
            'memo'           => $item->{ServiceTerms::MEMO},
            'start_at'       => $item->{ServiceTerms::START_AT},
            'end_at'         => $item->{ServiceTerms::END_AT},
        ];
    }
}
