<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

class CreateLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $connectionName = Config::get('myapp.database.logs.baseConnectionName');

        // 管理者系
        /**
         * admins_log table
         */
        Schema::connection($connectionName)->create('admins_log', function (Blueprint $table) {
            $table->id();
            // $table->foreignId('admin_id')->constrained('admins')->comment('管理者ID'); // DBを変える為指定出来ない
            $table->integer('admin_id')->comment('管理者ID');
            $table->string('function', 255)->comment('実行ファンクション');
            $table->tinyInteger('status')->comment('ステータス');
            $table->timestamp('action_time')->comment('実行日時');
            $table->timestamps();
            $table->softDeletes();

            $table->comment('admin action table');
        });

        // ユーザー系
        /**
         * user_coin_payment_log table
         */
        Schema::connection($connectionName)->create('user_coin_payment_log', function (Blueprint $table) {
            $table->integer('user_id')->comment('ユーザーID');
            $table->uuid('order_id')->comment('注文ID');
            $table->integer('coin_id')->comment('コインID');
            $table->integer('status')->comment('決済ステータス 1:決済開始, 2:決済中(入金待ち), 3:決済完了, 98:期限切れ, 99:注文キャンセル');
            $table->timestamps();
            $table->softDeletes();

            // プライマリキー設定
            $table->primary(['user_id', 'order_id']);

            $table->comment('user coin payment log table');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $connectionName = Config::get('myapp.database.logs.baseConnectionName');

        Schema::connection($connectionName)->dropIfExists('admins_log');
        Schema::connection($connectionName)->dropIfExists('user_coin_payment_log');
    }
}
