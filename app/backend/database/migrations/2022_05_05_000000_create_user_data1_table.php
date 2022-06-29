<?php

use Illuminate\Support\Facades\Config;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserData1Table extends Migration
{
    /** @var array<int, array<int, int>> NODE_NUMBERS ユーザー用DBのノード数(番号) */
    private const NODE_NUMBERS = [
        1 => [1, 4, 7, 10],
        2 => [2, 5, 8, 11],
        3 => [3, 6, 9, 12]
    ];

    /** @var string CONNECTION_NAME_FOR_CI CIなどで使う場合のコネクション名。単一のコネクションに接続させる。 */
    private const CONNECTION_NAME_FOR_CI = 'sqlite';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach (self::NODE_NUMBERS as $node => $shardIds) {
            // ex: mysql_user1 ..etc.
            $connectionName = self::getConnectionName($node);

            foreach ($shardIds as $shardId) {
                /**
                 * user_payments table
                 */
                Schema::connection($connectionName)->create('user_payments'.$shardId, function (Blueprint $table) {
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
                Schema::connection($connectionName)->create('user_comments'.$shardId, function (Blueprint $table) {
                    $table->id();
                    $table->integer('user_id')->comment('ユーザーID');
                    $table->text('comment')->comment('コメント文');
                    $table->timestamps();
                    $table->softDeletes();

                    $table->comment('about user comment table');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach (self::NODE_NUMBERS as $node => $shardIds) {
            $connectionName = self::getConnectionName($node);

            foreach ($shardIds as $shardId) {
                Schema::connection($connectionName)->dropIfExists('user_payments'.$shardId);
                Schema::connection($connectionName)->dropIfExists('user_comments'.$shardId);
            }
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
        $connectionName = Config::get('myapp.database.users.baseConnectionName');

        if ($connectionName === self::CONNECTION_NAME_FOR_CI) {
            return $connectionName;
        }

        return $connectionName . (string)$nodeNumber;
    }
}
