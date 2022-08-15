<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('ユーザー名');
            $table->string('email')->unique()->comment('メールアドレス');
            $table->timestamp('email_verified_at')->nullable()->comment('メールアドレス検証日');
            $table->string('password')->comment('パスワード');
            $table->tinyInteger('role')->unsigned()->default(0)->comment('ロール');
            $table->rememberToken()->comment('リメンバートークン');
            // $table->foreignId('current_team_id')->nullable()->comment('チームID');
            // $table->string('profile_photo_path', 2048)->nullable()->comment('プロフィールアイコンパス');
            $table->dateTime('created_at')->comment('登録日時');
            $table->dateTime('updated_at')->comment('更新日時');
            $table->dateTime('deleted_at')->nullable()->default(null)->comment('削除日時');
            // $table->timestamps();
            // $table->softDeletes();

            $table->comment('users table');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
