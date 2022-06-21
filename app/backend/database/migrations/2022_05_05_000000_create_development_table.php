<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDevelopmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * admins table
         */
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('管理者名');
            $table->string('email')->unique()->comment('メールアドレス');
            $table->timestamp('email_verified_at')->nullable()->comment('メールアドレス確認日時');
            $table->string('password')->comment('パスワード');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        /**
         * admins_log table
         */
        Schema::create('admins_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('admins')->comment('管理者ID');
            $table->string('function', 255)->comment('実行ファンクション');
            $table->string('status', 255)->comment('ステータス');
            $table->timestamp('action_time')->comment('実行日時');
            $table->timestamps();
            $table->softDeletes();
        });

        /**
         * permission table
         */
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('パーミッション名');
            $table->timestamps();
            $table->softDeletes();
        });

        /**
         * role table
         */
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('ロール名');
            $table->string('code')->comment('ロールコード名');
            $table->string('detail')->comment('詳細');
            $table->timestamps();
            $table->softDeletes();
        });

        /**
         * role_permissions table
         */
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('short_name')->comment('省略名');
            $table->foreignId('role_id')->constrained('roles')->comment('ロールID');
            $table->foreignId('permission_id')->constrained('permissions')->comment('パーミッションID');
            $table->timestamps();
            $table->softDeletes();
        });

        /**
         * admins_roles table
         */
        Schema::create('admins_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('admins')->comment('管理者ID');
            $table->foreignId('role_id')->constrained('roles')->unique()->comment('ロールID');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admins');
        Schema::dropIfExists('admins_log');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('admins_roles');
    }
}
