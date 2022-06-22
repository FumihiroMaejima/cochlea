<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserData2Table extends Migration
{
    /**
     * The name of the database connection to use.
     *
     * @var string|null
     */
    protected $connection = 'mysql_user2';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * user_payments table
         */
        Schema::create('user_payments', function (Blueprint $table) {
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
        Schema::create('user_comments', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->comment('ユーザーID');
            $table->text('comment')->comment('コメント文');
            $table->timestamps();
            $table->softDeletes();

            $table->comment('about user comment table');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_payments');
        Schema::dropIfExists('user_comments');
    }
}
