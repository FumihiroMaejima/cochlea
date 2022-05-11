<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductMasterModelTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * products table
         */
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->comment('商品名');
            $table->text('detail')->comment('詳細');
            $table->tinyInteger('type')->comment('商品の種類');
            $table->integer('price')->comment('価格');
            $table->string('unit', 255)->comment('単位');
            $table->string('manufacturer', 255)->comment('製造元');
            $table->dateTime('notice_start_at')->comment('予告開始日時');
            $table->dateTime('notice_end_at')->comment('予告終了日時');
            $table->dateTime('purchase_start_at')->comment('購入開始日時');
            $table->dateTime('purchase_end_at')->comment('購入終了日時');
            $table->string('image', 255)->comment('イメージ');
            $table->timestamps();
            $table->softDeletes();
        });

        /**
         * product_types table
         */
        Schema::create('product_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->comment('種類名');
            $table->text('detail')->comment('詳細');
            $table->timestamps();
            $table->softDeletes();
        });

        /**
         * manufactureres table
         */
        Schema::create('manufactureres', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->comment('製造元名');
            $table->text('detail')->comment('詳細');
            $table->string('address', 255)->comment('住所');
            $table->string('tel', 255)->comment('電話番号');
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
        Schema::dropIfExists('products');
        Schema::dropIfExists('product_types');
        Schema::dropIfExists('manufactureres');
    }
}
