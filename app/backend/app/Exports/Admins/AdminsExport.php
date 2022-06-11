<?php

namespace App\Exports\Admins;

use App\Models\Admins;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Collection;
use App\Repositories\Admins\Roles\RolesRepositoryInterface;

class AdminsExport implements FromCollection, WithHeadings, WithTitle, WithMapping
{
    use Exportable;
    private $resource;
    private $roleIds;
    private $roles;

    /**
     * constructer
     *
     * @param \Illuminate\Support\Collection $resource
     * @return void
     */
    public function __construct(Collection $resource)
    {
        $this->resource = $resource;
        $roles = app()->make(RolesRepositoryInterface::class)->getRolesList();
        $this->roleIds = $roles->pluck('id')->toArray();
        $this->roles = $roles->toArray();
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
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'id',
            '氏名',
            'メールアドレス',
            '権限'
        ];
    }

    /**
     * setting sheet title (Excel only)
     *
     * @return string
     */
    public function title(): string
    {
        return 'メンバー検索結果';
    }

    /**
     * 1行あたりのデータの設定を行う
     *
     * @return array
     */
    public function map($item): array
    {
        // return $data;
        return [
            'id'     => $item->id,
            'name'   => $item->name,
            'email'  => $item->email,
            // 'roleId' => $item->roleId,
            'roleId' => $this->getRoleName($item->roleId),
        ];
    }

    /**
     * idに紐付く権限名の取得
     *
     * @return string
     */
    public function getRoleName(int $id)
    {
        return $this->roles[array_search($id, $this->roleIds, true)]->name;
    }
}
