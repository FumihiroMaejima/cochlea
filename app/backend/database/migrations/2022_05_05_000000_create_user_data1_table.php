<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserData1Table extends Migration
{
    protected array $nodeNumbers = [1, 2, 3];
    protected string $baseConnectionName = 'mysql_user';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach ($this->nodeNumbers as $node) {
            /**
             * user_payments table
             */
            Schema::connection($this->getConnectionName($node))->create('user_payments', function (Blueprint $table) {
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
            Schema::connection($this->getConnectionName($node))->create('user_comments', function (Blueprint $table) {
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
        foreach ($this->nodeNumbers as $node) {
            Schema::connection($this->getConnectionName($node))->dropIfExists('user_payments');
            Schema::connection($this->getConnectionName($node))->dropIfExists('user_comments');
        }
    }


    /**
     * get connection name by node number.
     *
     * @param int $nodeNumber node number
     * @return string
     */
    public function getConnectionName(int $nodeNumber): string
    {
        return $this->baseConnectionName . (string)$nodeNumber;
    }
}
