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
        /**
         * users table
         * SNS認証の都合上、email,passwordカラムはnullableにしている
         */
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('ユーザー名');
            $table->string('email')->nullable()->default(null)->unique()->comment('メールアドレス');
            $table->timestamp('email_verified_at')->nullable()->comment('メールアドレス検証日');
            $table->string('password')->nullable()->default(null)->comment('パスワード');
            $table->tinyInteger('role')->unsigned()->default(0)->comment('ロール');
            $table->rememberToken()->comment('リメンバートークン');
            // $table->foreignId('current_team_id')->nullable()->comment('チームID');
            // $table->string('profile_photo_path', 2048)->nullable()->comment('プロフィールアイコンパス');
            $table->tinyInteger('is_left')->unsigned()->default(0)->comment('退会済みか');
            $table->dateTime('code_verified_at')->nullable()->default(null)->comment('認証コード検証日時');
            $table->dateTime('last_login_at')->nullable()->default(null)->comment('最終ログイン日時');
            $table->dateTime('created_at')->comment('登録日時');
            $table->dateTime('updated_at')->comment('更新日時');
            $table->dateTime('deleted_at')->nullable()->default(null)->comment('削除日時');
            // $table->timestamps();
            // $table->softDeletes();

            $table->comment('users table');
        });

        /**
         * oauth_users table
         */
        Schema::create('oauth_users', function (Blueprint $table) {
            $table->integer('user_id')->unsigned()->comment('ユーザーID');
            $table->tinyInteger('type')->unsigned()->comment('SNSタイプ');
            $table->integer('github_id')->unsigned()->nullable()->default(null)->comment('GithubID');
            $table->string('github_token')->nullable()->default(null)->comment('Githubトークン');
            $table->integer('twitter_id')->unsigned()->nullable()->default(null)->comment('TwitterID');
            $table->string('twitter_token')->nullable()->default(null)->comment('Twitterトークン');
            $table->integer('facebook_id')->unsigned()->nullable()->default(null)->comment('FaceBookID');
            $table->string('facebook_token')->nullable()->default(null)->comment('FaceBookトークン');
            $table->string('code')->nullable()->default(null)->comment('認証コード');
            $table->string('state')->nullable()->default(null)->comment('認証ステータス');
            $table->dateTime('created_at')->comment('登録日時');
            $table->dateTime('updated_at')->comment('更新日時');
            $table->dateTime('deleted_at')->nullable()->default(null)->comment('削除日時');

            // プライマリキー設定
            $table->primary(['user_id']);
            // ユニークキー設定
            $table->unique(['github_id']);
            $table->unique(['twitter_id']);
            $table->unique(['facebook_id']);

            $table->comment('oauth_users table');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('oauth_users');
        Schema::dropIfExists('users');
    }
};
