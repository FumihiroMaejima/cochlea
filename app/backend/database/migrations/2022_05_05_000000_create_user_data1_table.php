<?php

use Illuminate\Support\Facades\Config;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Library\Database\ShardingLibrary;

class CreateUserData1Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach (ShardingLibrary::getShardingSetting() as $node => $shardIds) {
            // ex: mysql_user1 ..etc.
            $connectionName = ShardingLibrary::getConnectionByNodeNumber($node);

            foreach ($shardIds as $shardId) {
                /**
                 * user_auth_codes table
                 */
                Schema::connection($connectionName)->create('user_auth_codes'.$shardId, function (Blueprint $table) {
                    $table->integer('user_id')->unsigned()->comment('ユーザーID');
                    $table->tinyInteger('type')->unsigned()->comment('認証種類');
                    $table->tinyInteger('count')->unsigned()->default(0)->comment('試行回数');
                    $table->integer('code')->unsigned()->comment('認証コード');
                    $table->tinyInteger('is_used')->unsigned()->default(0)->comment('使用済みか');
                    $table->dateTime('expired_at')->comment('有効期限日時');
                    $table->dateTime('created_at')->useCurrent()->comment('登録日時');
                    $table->dateTime('updated_at')->useCurrentOnUpdate()->comment('更新日時');

                    // プライマリキー設定
                    $table->primary(['user_id', 'code']);

                    $table->comment('about user auth codes table');
                });

                /**
                 * user_coin_histories table
                 */
                Schema::connection($connectionName)->create('user_coin_histories'.$shardId, function (Blueprint $table) {
                    $table->integer('user_id')->unsigned()->comment('ユーザーID');
                    $table->uuid('uuid')->comment('UUID(ユーザーごとに一意)');
                    $table->tinyInteger('type')->unsigned()->comment('履歴タイプ 1:購入、2:獲得、3:消費、4:期限切れ、5:補填');
                    $table->integer('get_free_coins')->unsigned()->default(0)->comment('獲得した無料コイン数');
                    $table->integer('get_paid_coins')->unsigned()->default(0)->comment('購入・獲得した有料コイン数');
                    $table->integer('get_limited_time_coins')->unsigned()->default(0)->comment('購入・獲得した期間限定コイン数');
                    $table->integer('used_free_coins')->unsigned()->default(0)->comment('消費した無料コイン数');
                    $table->integer('used_paid_coins')->unsigned()->default(0)->comment('消費した有料コイン数');
                    $table->integer('used_limited_time_coins')->unsigned()->default(0)->comment('消費した期間限定コイン数');
                    $table->integer('expired_limited_time_coins')->unsigned()->default(0)->comment('期限切れコイン数');
                    $table->dateTime('expired_at')->nullable()->default(null)->comment('期間限定コインの使用期限日時');
                    $table->uuid('order_id')->nullable()->default(null)->comment('(購入時)注文ID(UUID)');
                    $table->integer('product_id')->default(0)->comment('プロダクトID');
                    $table->dateTime('created_at')->useCurrent()->comment('登録日時');
                    $table->dateTime('updated_at')->useCurrentOnUpdate()->comment('更新日時');
                    $table->dateTime('deleted_at')->nullable()->default(null)->comment('削除日時');

                    // プライマリキー設定
                    $table->primary(['user_id', 'created_at']);
                    // uniqueキー設定
                    $table->unique(['user_id', 'uuid']);

                    $table->comment('about user coins history table');
                });

                /**
                 * user_coin_payment_status table
                 */
                Schema::connection($connectionName)->create('user_coin_payment_status'.$shardId, function (Blueprint $table) {
                    $table->integer('user_id')->unsigned()->comment('ユーザーID');
                    $table->uuid('order_id')->comment('注文ID(UUID)');
                    $table->integer('coin_id')->unsigned()->comment('コインID');
                    $table->integer('status')->unsigned()->comment('決済ステータス 1:決済開始, 2:決済中(入金待ち), 3:決済完了, 98:期限切れ, 99:注文キャンセル');
                    $table->string('payment_service_id', 255)->comment('決済サービスの決済id(stripeのセッションidなど)');
                    $table->dateTime('created_at')->useCurrent()->comment('登録日時');
                    $table->dateTime('updated_at')->useCurrentOnUpdate()->comment('更新日時');
                    $table->dateTime('deleted_at')->nullable()->default(null)->comment('削除日時');

                    // プライマリキー設定
                    // $table->unique(['user_id', 'order_id']); // UNIQUE KEY `user_coin_payment_status*_user_id_order_id_unique` (`user_id`,`order_id`)
                    $table->primary(['user_id', 'order_id', 'created_at']); // PRIMARY KEY (`user_id`,`order_id`)

                    $table->comment('about user coin payment status table');
                });

                /**
                 * user_coins table
                 */
                Schema::connection($connectionName)->create('user_coins'.$shardId, function (Blueprint $table) {
                    $table->integer('user_id')->unsigned()->comment('ユーザーID');
                    $table->integer('free_coins')->unsigned()->default(0)->comment('無料コイン数');
                    $table->integer('paid_coins')->unsigned()->default(0)->comment('有料コイン数');
                    $table->integer('limited_time_coins')->unsigned()->default(0)->comment('期間限定コイン数');
                    $table->dateTime('created_at')->useCurrent()->comment('登録日時');
                    $table->dateTime('updated_at')->useCurrentOnUpdate()->comment('更新日時');
                    $table->dateTime('deleted_at')->nullable()->default(null)->comment('削除日時');

                    // プライマリキー設定
                    $table->primary(['user_id']);

                    $table->comment('about user coins table');
                });

                /**
                 * user_payments table
                 */
                Schema::connection($connectionName)->create('user_payments'.$shardId, function (Blueprint $table) {
                    $table->id();
                    $table->integer('user_id')->unsigned()->comment('ユーザーID');
                    $table->integer('product_id')->unsigned()->comment('製品ID');
                    $table->integer('price')->unsigned()->comment('価格');
                    $table->dateTime('created_at')->useCurrent()->comment('登録日時');
                    $table->dateTime('updated_at')->useCurrentOnUpdate()->comment('更新日時');
                    $table->dateTime('deleted_at')->nullable()->default(null)->comment('削除日時');

                    $table->comment('about user payment table');
                });

                /**
                 * user_comments table
                 */
                Schema::connection($connectionName)->create('user_comments'.$shardId, function (Blueprint $table) {
                    $table->id();
                    $table->integer('user_id')->unsigned()->comment('ユーザーID');
                    $table->text('comment')->comment('コメント文');
                    $table->dateTime('created_at')->useCurrent()->comment('登録日時');
                    $table->dateTime('updated_at')->useCurrentOnUpdate()->comment('更新日時');
                    $table->dateTime('deleted_at')->nullable()->default(null)->comment('削除日時');

                    $table->comment('about user comment table');
                });

                /**
                 * user_read_informations table
                 */
                Schema::connection($connectionName)->create('user_read_informations'.$shardId, function (Blueprint $table) {
                    $table->integer('user_id')->unsigned()->comment('ユーザーID');
                    $table->integer('information_id')->unsigned()->comment('お知らせID');
                    $table->dateTime('created_at')->useCurrent()->comment('登録日時');
                    $table->dateTime('updated_at')->useCurrentOnUpdate()->comment('更新日時');
                    $table->dateTime('deleted_at')->nullable()->default(null)->comment('削除日時');

                    // プライマリキー設定
                    $table->primary(['user_id', 'information_id', 'created_at'], 'user_read_informations1_primary');

                    $table->comment('about user read informations table');
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
        foreach (ShardingLibrary::getShardingSetting() as $node => $shardIds) {
            $connectionName = ShardingLibrary::getConnectionByNodeNumber($node);

            foreach ($shardIds as $shardId) {
                Schema::connection($connectionName)->dropIfExists('user_auth_codes'.$shardId);
                Schema::connection($connectionName)->dropIfExists('user_coin_histories'.$shardId);
                Schema::connection($connectionName)->dropIfExists('user_coin_payment_status'.$shardId);
                Schema::connection($connectionName)->dropIfExists('user_coins'.$shardId);
                Schema::connection($connectionName)->dropIfExists('user_payments'.$shardId);
                Schema::connection($connectionName)->dropIfExists('user_comments'.$shardId);
                Schema::connection($connectionName)->dropIfExists('user_read_informations'.$shardId);
            }
        }
    }
}
