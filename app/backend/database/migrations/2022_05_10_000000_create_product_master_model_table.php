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
        Schema::create('coins', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->comment('コイン名');
            $table->text('detail')->comment('詳細');
            $table->integer('price')->unsigned()->comment('コインの購入価格');
            $table->integer('cost')->unsigned()->comment('アプリケーション内のコインの価格');
            $table->dateTime('start_at')->comment('公開開始日時');
            $table->dateTime('end_at')->comment('公開終了日時');
            $table->string('image', 255)->comment('イメージ');
            $table->dateTime('created_at')->comment('登録日時');
            $table->dateTime('updated_at')->comment('更新日時');
            $table->dateTime('deleted_at')->nullable()->default(null)->comment('削除日時');

            $table->comment('coins table');
        });

        /**
         * banners table
         */
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique()->comment('uuid');
            $table->string('name', 255)->comment('バナー名');
            $table->text('detail')->comment('詳細');
            $table->string('location', 255)->comment('配置場所');
            $table->integer('pc_height')->unsigned()->comment('PCサイズの高さ');
            $table->integer('pc_width')->unsigned()->comment('PCサイズの幅');
            $table->integer('sp_height')->unsigned()->comment('SPサイズの高さ');
            $table->integer('sp_width')->unsigned()->comment('SPサイズの幅');
            $table->dateTime('start_at')->comment('公開開始日時');
            $table->dateTime('end_at')->comment('公開終了日時');
            $table->dateTime('created_at')->comment('登録日時');
            $table->dateTime('updated_at')->comment('更新日時');
            $table->dateTime('deleted_at')->nullable()->default(null)->comment('削除日時');

            // index設定
            $table->index('location');

            $table->comment('banners table');
        });

        /**
         * events table
         */
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->comment('イベント名');
            $table->tinyInteger('type')->unsigned()->comment('イベントタイプ');
            $table->text('detail')->comment('詳細');
            $table->dateTime('start_at')->comment('公開開始日時');
            $table->dateTime('end_at')->comment('公開終了日時');
            $table->dateTime('created_at')->comment('登録日時');
            $table->dateTime('updated_at')->comment('更新日時');
            $table->dateTime('deleted_at')->nullable()->default(null)->comment('削除日時');

            $table->comment('events table');
        });

        /**
         * images table
         */
        Schema::create('images', function (Blueprint $table) {
            $table->id();
            // $table->uuid()->unique()->primary()->comment('uuid');
            $table->uuid()->unique()->comment('uuid');
            $table->string('name', 255)->comment('オリジナルファイル名');
            $table->string('extention', 255)->comment('拡張子');
            $table->string('mime_type', 255)->comment('mimeType');
            $table->string('s3_key', 255)->nullable()->comment('AWS S3のkey');
            $table->integer('version')->unsigned()->comment('ファイルのバージョン(更新日時のタイムスタンプ)');
            $table->dateTime('created_at')->comment('登録日時');
            $table->dateTime('updated_at')->comment('更新日時');
            $table->dateTime('deleted_at')->nullable()->default(null)->comment('削除日時');

            // index設定
            $table->index('s3_key');

            $table->comment('images table');
        });

        /**
         * informations table
         */
        Schema::create('informations', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->comment('お知らせ名');
            $table->tinyInteger('type')->unsigned()->comment('お知らせタイプ 1:お知らせ、2:メンテナンス、3:障害');
            $table->text('detail')->comment('詳細');
            $table->dateTime('start_at')->comment('公開開始日時');
            $table->dateTime('end_at')->comment('公開終了日時');
            $table->dateTime('created_at')->comment('登録日時');
            $table->dateTime('updated_at')->comment('更新日時');
            $table->dateTime('deleted_at')->nullable()->default(null)->comment('削除日時');

            $table->comment('informations table');
        });

        /**
         * products table
         */
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->comment('商品名');
            $table->text('detail')->comment('詳細');
            $table->tinyInteger('type')->unsigned()->comment('商品の種類');
            $table->integer('price')->unsigned()->comment('価格');
            $table->string('unit', 255)->comment('単位');
            $table->string('manufacturer', 255)->comment('製造元');
            $table->dateTime('notice_start_at')->comment('予告開始日時');
            $table->dateTime('notice_end_at')->comment('予告終了日時');
            $table->dateTime('purchase_start_at')->comment('購入開始日時');
            $table->dateTime('purchase_end_at')->comment('購入終了日時');
            $table->string('image', 255)->comment('イメージ');
            $table->dateTime('created_at')->comment('登録日時');
            $table->dateTime('updated_at')->comment('更新日時');
            $table->dateTime('deleted_at')->nullable()->default(null)->comment('削除日時');

            $table->comment('products table');
        });

        /**
         * product_types table
         */
        Schema::create('product_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->comment('種類名');
            $table->text('detail')->comment('詳細');
            $table->dateTime('created_at')->comment('登録日時');
            $table->dateTime('updated_at')->comment('更新日時');
            $table->dateTime('deleted_at')->nullable()->default(null)->comment('削除日時');

            $table->comment('type of product table');
        });

        /**
         * manufacturers table
         */
        Schema::create('manufacturers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->comment('製造元名');
            $table->text('detail')->comment('詳細');
            $table->string('address', 255)->comment('住所');
            $table->string('tel', 255)->comment('電話番号');
            $table->dateTime('created_at')->comment('登録日時');
            $table->dateTime('updated_at')->comment('更新日時');
            $table->dateTime('deleted_at')->nullable()->default(null)->comment('削除日時');

            $table->comment('manufacturers table');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coins');
        Schema::dropIfExists('banners');
        Schema::dropIfExists('events');
        Schema::dropIfExists('images');
        Schema::dropIfExists('informations');
        Schema::dropIfExists('products');
        Schema::dropIfExists('product_types');
        Schema::dropIfExists('manufactureres');
    }
}
