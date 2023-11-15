<?php

declare(strict_types=1);

namespace App\Exports\Admins;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Collection;
use App\Repositories\Masters\Permissions\PermissionsRepositoryInterface;
use App\Trait\ProcessingRoleDataTrait;

class RolesExport implements FromCollection, WithHeadings, WithTitle, WithMapping
{
    use Exportable;
    use ProcessingRoleDataTrait;

    private $resource;
    private $permissions;
    private $permissionIds;

    /**
     * @param \Illuminate\Support\Collection $resource
     * @return void
     */
    public function __construct(Collection $collection)
    {
        $this->resource = $collection;
        $permissions = app()->make(PermissionsRepositoryInterface::class)->getPermissionsList();
        $this->permissionIds = $permissions->pluck('id')->toArray();
        $this->permissions = $permissions->toArray();
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
            'ロール名',
            'コード名',
            '詳細名',
            'パーミッション'
        ];
    }

    /**
     * setting sheet title (Excel only)
     * @return string
     */
    public function title(): string
    {
        return 'ロール検索結果';
    }

    /**
     * 1行あたりのデータの設定を行う
     * @return string
     */
    public function map($item): array
    {
        // return $data;
        return [
            'id'     => $item->id,
            'name'   => $item->name,
            'code'   => $item->code,
            'detail' => $item->detail,
            'permissions' => $this->getPermissionName(
                is_null($item->permissions)
                    ? []
                    : explode(',', $item->permissions)
            ),
        ];
    }

    /**
     * idに紐付くパーミッション名の取得
     * @param array $ids
     * @return string
     */
    public function getPermissionName(array $ids)
    {
        return $this->processingPermissions($ids, $this->permissions, $this->permissionIds);
    }
}
