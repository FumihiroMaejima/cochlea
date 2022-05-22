<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class RolePermissionsTableSeeder extends Seeder
{
    private const TABLE_NAME = 'role_permissions';
    private const SEEDER_DATA_LENGTH = 12;
    private int $count = 12;
    private int $masterCount = 4;
    private int $adminCount = 8;
    private int $developCount = 11;
    private int $readOnlyCount = 12;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $template = [
        //     'name'          => '',
        //     'role_id'       => 1,
        //     'permission_id' => 1,
        //     'created_at'    => '2021-01-14 00:00:00',
        //     'updated_at'    => '2021-01-14 00:00:00'
        // ];

        $roles = Config::get('myapp.seeder.authority.roles');

        $permissions = Config::get('myapp.seeder.authority.permissions');

        // insert用データ
        $data = [];

        // 1~$this->countの数字の配列でforを回す
        foreach (range(1, $this->count) as $i) {
            // $row = $template;
            $row = [];

            // ロール順にロールとパーミッションの割り当てを行う
            if ($i <= $this->masterCount) {
                // マスターの場合
                $row = $this->makeRowResource($roles[0], $permissions[$i - 1]);
            } elseif (($this->masterCount < $i) && ($i <= $this->adminCount)) {
                // 管理者の場合
                $row = $this->makeRowResource($roles[1], $permissions[$i - 5]);
            } elseif (($this->adminCount < $i) && ($i <= $this->developCount)) {
                // 開発者の場合
                $row = $this->makeRowResource($roles[2], $permissions[$i - 9]);
            } elseif (($i === $this->readOnlyCount)) {
                // 読取専用の場合
                $row = $this->makeRowResource($roles[3], $permissions[1]);
            }

            $data[] = $row;
        }

        // テーブルへの格納
        DB::table(self::TABLE_NAME)->insert($data);
    }

    /**
     * make row data.
     * @param object $role
     * @param object $permission
     *
     * @return array
     */
    private function makeRowResource(object $role, object $permission): array
    {
        return [
            'name'          => $role->name . '_' . $permission->name,
            'short_name'    => $permission->name,
            'role_id'       => $role->key,
            'permission_id' => $permission->key,
            'created_at'    => '2021-01-14 00:00:00',
            'updated_at'    => '2021-01-14 00:00:00'
        ];
    }
}
