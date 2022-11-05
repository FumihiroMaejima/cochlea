<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use App\Library\Database\LogTablesLibrary;

class CreateLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // $connectionName = Config::get('myapp.database.logs.baseConnectionName');
        $connectionName = LogTablesLibrary::getLogDatabaseConnection();

        // 管理者系
        /**
         * admins_log table
         */
        Schema::connection($connectionName)->create('admins_log', function (Blueprint $table) {
            $table->id();
            // $table->foreignId('admin_id')->constrained('admins')->comment('管理者ID'); // DBを変える為指定出来ない
            $table->integer('admin_id')->unsigned()->comment('管理者ID');
            $table->string('function', 255)->comment('実行ファンクション');
            $table->tinyInteger('status')->unsigned()->comment('ステータス');
            $table->dateTime('action_time')->comment('実行日時');
            $table->dateTime('created_at')->comment('登録日時');
            $table->dateTime('updated_at')->comment('更新日時');
            $table->dateTime('deleted_at')->nullable()->default(null)->comment('削除日時');

            $table->comment('admin action table');
        });

        // ユーザー系
        /**
         * user_coin_payment_log table
         */
        Schema::connection($connectionName)->create('user_coin_payment_log', function (Blueprint $table) {
            $table->integer('user_id')->unsigned()->comment('ユーザーID');
            $table->uuid('order_id')->comment('注文ID(UUID)');
            $table->integer('coin_id')->unsigned()->comment('コインID');
            $table->integer('status')->unsigned()->comment('決済ステータス 1:決済開始, 2:決済中(入金待ち), 3:決済完了, 98:期限切れ, 99:注文キャンセル');
            $table->dateTime('created_at')->comment('登録日時');
            $table->dateTime('updated_at')->comment('更新日時');
            $table->dateTime('deleted_at')->nullable()->default(null)->comment('削除日時');

            // プライマリキー設定
            $table->primary(['user_id', 'order_id', 'created_at']);

            $table->comment('user coin payment log table');
        });

        /**
         * user_read_information_log table
         */
        Schema::connection($connectionName)->create('user_read_information_log', function (Blueprint $table) {
            $table->integer('user_id')->unsigned()->comment('ユーザーID');
            $table->integer('information_id')->unsigned()->comment('お知らせID');
            $table->dateTime('created_at')->comment('登録日時');
            $table->dateTime('updated_at')->comment('更新日時');
            $table->dateTime('deleted_at')->nullable()->default(null)->comment('削除日時');

            // プライマリキー設定
            $table->primary(['user_id', 'information_id', 'created_at'], 'user_read_information_log_primary');

            $table->comment('user read information log table');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // $connectionName = Config::get('myapp.database.logs.baseConnectionName');
        $connectionName = LogTablesLibrary::getLogDatabaseConnection();

        Schema::connection($connectionName)->dropIfExists('admins_log');
        Schema::connection($connectionName)->dropIfExists('user_coin_payment_log');
        Schema::connection($connectionName)->dropIfExists('user_read_information_log');
    }
}
