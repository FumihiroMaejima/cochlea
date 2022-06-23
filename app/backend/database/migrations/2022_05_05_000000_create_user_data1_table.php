<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserData1Table extends Migration
{
    /** @var array<int, int> NODE_NUMBERS ユーザー用DBのノード数(番号) */
    private const NODE_NUMBERS = [1, 2, 3];

    /** @var string BASE_CONNECTION_NAME DBへのコネクション名のベース(prefix) */
    private const BASE_CONNECTION_NAME = 'mysql_user';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach (self::NODE_NUMBERS as $node) {
            // ex: mysql_user1 ..etc.
            $connectionName = self::getConnectionName($node);

            /**
             * user_payments table
             */
            Schema::connection($connectionName)->create('user_payments', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id')->comment('ユーザーID');
                $table->integer('product_id')->comment('製品ID');
                $table->integer('price')->comment('価格');
                $table->timestamps();
                $table->softDeletes();

                $table->comment('about user payment table');
            });

            /**
             * user_comments table
             */
            Schema::connection($connectionName)->create('user_comments', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id')->comment('ユーザーID');
                $table->text('comment')->comment('コメント文');
                $table->timestamps();
                $table->softDeletes();

                $table->comment('about user comment table');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach (self::NODE_NUMBERS as $node) {
            $connectionName = self::getConnectionName($node);

            Schema::connection($connectionName)->dropIfExists('user_payments');
            Schema::connection($connectionName)->dropIfExists('user_comments');
        }
    }


    /**
     * get connection name by node number.
     *
     * @param int $nodeNumber node number
     * @return string
     */
    public static function getConnectionName(int $nodeNumber): string
    {
        return self::BASE_CONNECTION_NAME . (string)$nodeNumber;
    }
}
