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
                 * user_coin_payment_status table
                 */
                Schema::connection($connectionName)->create('user_coin_payment_status'.$shardId, function (Blueprint $table) {
                    $table->integer('user_id')->comment('ユーザーID');
                    $table->uuid('order_id')->comment('注文ID');
                    $table->integer('coin_id')->comment('コインID');
                    $table->integer('status')->comment('決済ステータス 1:決済開始, 2:決済中(入金待ち), 3:決済完了, 99:注文キャンセル,');
                    // $table->string('stripe_session_id', 255)->comment('stripeのセッションID');
                    $table->timestamps();
                    $table->softDeletes();

                    // プライマリキー設定
                    // $table->unique(['user_id', 'order_id']); // UNIQUE KEY `user_coin_payment_status*_user_id_order_id_unique` (`user_id`,`order_id`)
                    $table->primary(['user_id', 'order_id']); // PRIMARY KEY (`user_id`,`order_id`)

                    $table->comment('about user coin payment status table');
                });

                /**
                 * user_coins table
                 */
                Schema::connection($connectionName)->create('user_coins'.$shardId, function (Blueprint $table) {
                    $table->integer('user_id')->comment('ユーザーID');
                    $table->integer('free_coins')->comment('無料コイン数');
                    $table->integer('paid_coins')->comment('有料コイン数');
                    $table->integer('limited_time_coins')->comment('期間限定コイン数');
                    $table->timestamps();
                    $table->softDeletes();

                    // プライマリキー設定
                    $table->primary(['user_id']);

                    $table->comment('about user coins table');
                });

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
                Schema::connection($connectionName)->dropIfExists('user_coin_payment_status'.$shardId);
                Schema::connection($connectionName)->dropIfExists('user_coins'.$shardId);
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
