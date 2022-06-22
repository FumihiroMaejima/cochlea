<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogsTable extends Migration
{
    /**
     * The name of the database connection to use.
     *
     * @var string|null
     */
    protected $connection = 'mysql_logs';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        /**
         * admins_log table
         */
        Schema::create('admins_log', function (Blueprint $table) {
            $table->id();
            // $table->foreignId('admin_id')->constrained('admins')->comment('管理者ID'); // DBを変える為指定出来ない
            $table->integer('admin_id')->comment('管理者ID');
            $table->string('function', 255)->comment('実行ファンクション');
            $table->string('status', 255)->comment('ステータス');
            $table->timestamp('action_time')->comment('実行日時');
            $table->timestamps();
            $table->softDeletes();

            $table->comment('admin action table');
        });



    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admins_log');
    }
}
