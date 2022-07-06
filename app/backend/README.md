# Laravel Docker Environmental

Laravel環境をDockerで構築する為の手順書

# 構成

| 名前 | バージョン |
| :--- | :---: |
| PHP | 8.0.15(php:8.0.15-fpm-alpine) |
| MySQL | 5.7 |
| Nginx | 1.19(nginx:1.19-alpine) |
| Laravel | 9.* |

---
# ローカル環境の構築(Mac)

## PHPのバージョン更新

```shell-session
$ brew search php@7
==> Formulae
php@7.2                    php@7.3                    php@7.4

$ brew install php@7.4
```

インストール中に下記のメッセージがある。
下記のメッセージを頼りに$PATHと設定する。

```shell-session
If you need to have apr first in your PATH run:
  echo 'export PATH="/usr/local/opt/apr/bin:$PATH"' >> ~/.bash_profile
```

「~/.bash_profile」にPATHの設定を追記。
「~/.bash_profile」の読み込み。

```shell-session
$ echo 'export PATH="/usr/local/opt/apr/bin:$PATH"' >> ~/.bash_profile
$ source ~/.bash_profile
```

PHPのサービスの起動。

```shell-session
$ brew services start php
==> Successfully started `php` (label: homebrew.mxcl.php)
```

更新の確認。

```shell-session
$ php -v
PHP 7.4.4 (cli) (built: Mar 19 2020 20:14:52) ( NTS )
Copyright (c) The PHP Group
Zend Engine v3.4.0, Copyright (c) Zend Technologies
    with Zend OPcache v7.4.4, Copyright (c), by Zend Technologies
```

PHPのマイナーアップデートをかける場合(git等もアップデートされる。)

```shell-session
$ brew upgrade php

~ $ php -v
PHP 7.4.8 (cli) (built: Jul 30 2020 02:08:45) ( NTS )
Copyright (c) The PHP Group
Zend Engine v3.4.0, Copyright (c) Zend Technologies
    with Zend OPcache v7.4.8, Copyright (c), by Zend Technologies
```



## Composerのインストール

opensslのインストール

```shell-session
$ brew install openssl
```

Composerのインストール

```shell-session
$ curl -sS https://getcomposer.org/installer | php
```

インストールしたファイルを「/usr/local/bin/」に移動させる。

```shell-session
$ ls
composer.phar
$ mv composer.phar /usr/local/bin/composer
```

インストールの確認

```shell-session
$ composer --version
Composer version 1.10.4 2020-04-09 17:05:50
```
---

## xdebugの設定

Dockerfileで下記の通りにxdebugをインストールする。

```shell-session
$  docker-php-ext-enable xdebug
```

コンテナにマウントするphp.iniに下記の通り、xdebugの設定を行う。(v3の書き方)

```shell-session
[xdebug]
# version 3
xdebug.mode=debug
xdebug.client_host=host.docker.internal
xdebug.client_port=9010
xdebug.start_with_request=yes
xdebug.log=/tmp/xdebug.log
xdebug.discover_client_host=0
```

.vscode/launch.jsonに下記の通り、設定を行う。

```json
{
    ...
    "configurations": [
        {
            "name": "Listen for XDebug(setting custom name.)",
            "type": "php",
            "request": "launch",
            // set on php.ini
            "port": 9010,
            "pathMappings": {
                // {docker container document root}:{local document root}
                "/var/www/html": "/path/to/project"
            }
        }
    ]
}
```

---
# 開発環境構築

## プロジェクト新規作成直後に必須の作業

### laravel_dockerリポジトリのclone

```shell-session
$ git clone https://github.com/FumihiroMaejima/laravel_docker your_project
```

### masterブランチのチェックアウト

デフォルトブランチがdevelopの為、masterブランチをチェックアウトする。

```shell-session
$ git checkout -b master remotes/origin/master
```

### 現在のremoteのURLの確認

```shell-session
$ git remote -v
origin	https://github.com/FumihiroMaejima/laravel_docker (fetch)
origin	https://github.com/FumihiroMaejima/laravel_docker (push)
```

### remoteリポジトリのURLの変更

```shell-session
$ git remote set-url origin https://github.com/Your_Name/your_project
$ git remote -v
origin	https://github.com/Your_Name/your_project (fetch)
origin	https://github.com/Your_Name/your_project (push)
```

### 注意点
git のコミットログを初期化もしくは削除すること。もしくはリベース。

### masterとdevelopブランチをremoteにpushする。

```shell-session
$ git push origin master
$ git push origin develop
```

### git-flowの初期化を行う。

```shell-session
$ git flow init
```


### env_exampleをコピペして.envを作る。
APP_PORTは現状設定不要。
nginxのポート設定は要注意が必要。


### Laravel version8系のプロジェクトを用意する場合

既存の「backend」ディレクトリをリネームして新しく作成する

```shell-session
$ composer create-project laravel/laravel=7.* --prefer-dist backend
```

## 不要ファイルの削除

＊コンテナイメージの作り直し時も同様

```shell-session
$ docker-compose down --rmi all
$ docker-compose down
$ docker-compose up -d

```

---
## Laravelプロジェクトの新規作成

dockerコンテナとマウントする為の「backend」ディレクトリはローカルで作成する。
「app」ディレクトリに移動してcomposerでプロジェクトを新規作成する。

バージョン:8.*

プロジェクト名:backend

*(フロントエンドとの連携を考慮しての命名)

```shell-session
$ cd app
$ composer create-project laravel/laravel=8.* --prefer-dist backend
```

## パッケージのインストール

バージョン7系以降をインストールする場合
「GuzzleHttpClient」はバージョン7系だとデフォルトでインストールされる。

*2022/05/01現在、「nunomaduro/phpinsights」は依存関係の都合からインストールが出来なかった。

```shell-session
$ composer require guzzlehttp/guzzle
$ composer require --dev nunomaduro/phpinsights
$ composer require --dev barryvdh/laravel-debugbar
$ composer require --dev friendsofphp/php-cs-fixer
$ composer require --dev squizlabs/php_codesniffer
$ composer require --dev phpmd/phpmd
$ composer require --dev codedungeon/phpunit-result-printer
$ composer require --dev barryvdh/laravel-ide-helper

# one liner
$ composer require --dev nunomaduro/phpinsights barryvdh/laravel-debugbar friendsofphp/php-cs-fixer squizlabs/php_codesniffer phpmd/phpmd codedungeon/phpunit-result-printer barryvdh/laravel-ide-helper
```

php-cs-fixer,phpcs,phpmdの設定ファイルを格納する

```shell-session
backend/.php-cs-fixer.php
backend/phpcs.xml
backend/ruleset.xml
```

CI関係のコマンド

```shell-session
vendor/bin/phpunit --testdox
vendor/bin/php-cs-fixer fix -v --diff ./src
vendor/bin/phpcs --standard=phpcs.xml --extensions=php .
vendor/bin/phpmd . text ruleset.xml --suffixes php --exclude node_modules,resources,storage,vendor
```

単体のファイルにphpunitをかける

```shell-session
vendor/bin/phpunit tests/Unit/ExampleTest --testdox
```

カバレッジ出力

docker コマンド経由で実行する場合は相対パスが変わる。

```shell-session
vendor/bin/phpunit --coverage-text --colors=never > storage/logs/coverage.log
docker-compose exec app vendor/bin/phpunit --coverage-text --colors=never > app/backend/storage/logs/coverage.log
```

`--testdox`と`--coverage-text`を同時に指定すると、textdoxの内容がcoverage.logに出力される。

コンソールには出力されない。

```shell-session
vendor/bin/phpunit --testdox --coverage-text --colors=never > storage/logs/coverage.log
```

カバレッジの出力には、ENVかphp.iniに下記の設定が必要。

```shell-session
XDEBUG_MODE=coverage or xdebug.mode=coverage
```


## マイグレーションについて

backend/.envの値はプロジェクトrootの.envの値に合わせること。
DB_HOSTはdocker.compose.ymlのmysqlコンテナの名前と同様になる。

```shell-session
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=
```

マイグレーションは必ずコンテナの内部で実行すること

```shell-session
$ docker-compose exec app php artisan migrate
Migration table created successfully.
Migrating: 2014_10_12_000000_create_users_table
Migrated:  2014_10_12_000000_create_users_table (0.07 seconds)
Migrating: 2019_08_19_000000_create_failed_jobs_table
Migrated:  2019_08_19_000000_create_failed_jobs_table (0.03 seconds)
```

DBのテーブル内の状態を初期化したい場合は、refreshコマンドを使う。

データベース全体を作り直すことが出来る。

```shell
$ docker-compose exec app php artisan migrate:refresh
Rolling back: 2019_08_19_000000_create_failed_jobs_table
Rolled back:  2019_08_19_000000_create_failed_jobs_table (0.08 seconds)
Rolling back: 2014_10_12_000000_create_users_table
Rolled back:  2014_10_12_000000_create_users_table (0.03 seconds)
Migrating: 2014_10_12_000000_create_users_table
Migrated:  2014_10_12_000000_create_users_table (0.06 seconds)
Migrating: 2019_08_19_000000_create_failed_jobs_table
Migrated:  2019_08_19_000000_create_failed_jobs_table (0.03 seconds)
```

データベースをリフレッシュし、全データベースシードを実行する

```shell-session
$ php artisan migrate:refresh --seed
```

データベースから全テーブルをドロップする。

```shell-session
$ php artisan migrate:fresh (--seed)
```

---

## app.phpの設定について

### timezoneの変更

```php
// 変更前
'timezone' => 'UTC',

// 変更後
'timezone' => 'Asia/Tokyo',
```

---
## 認証機能作成について

一度、migrate:freshなど実行しておくと良い。


laravel/uiのインストール(Larevel 8以降は不要。)

メモリ消費量が大きい為、コンテナ側で実行する。(php.iniの設定)

```shell-session
$ docker-compose exec app composer require laravel/ui
```

認証系のファイルの作成

```shell-session
$ php artisan ui vue --auth
```

Laravel8からlaravel/uiで認証を使わなくなった。(上記は不要)
`jetstream`を使う

```shell-session
$ composer require laravel/jetstream
$ php artisan jetstream:install livewire
```

マイグレーションの実行

```shell-session
$ docker-compose exec app php artisan migrate
```

アセットのコンパイル

```shell-session
$ npm install
$ npm run dev or npm run production
```

上記でデフォルトの認証機能が作成出来る。


## Json Web Tokens(JWT)の設定について

`firebase/php-jwt`と言うものもあるが証明書が必要らしいので割愛する。

`tymon/jwt-auth`のインストール。(バージョン指定必須)

```shell-session
$ composer require tymon/jwt-auth ^1.0.2
```

config/jwt.phpの作成

```shell-session
$ php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"
```

JWTで用いる秘密鍵の作成

```shell-session
$ php artisan jwt:secret
```

⇨「.env」に「JWT_SECRET」のパラメーターが追加される。

config/auth.phpの設定

「defaults」の「guard」を「api」に、「guards」の「api」の「driver」を「jwt」に変更する。


```PHP
  'defaults' => [
      'guard' => 'api',
      'passwords' => 'users',
  ],

  'guards' => [
      'web' => [
          'driver' => 'session',
          'provider' => 'users',
      ],

      'api' => [
          'driver' => 'jwt',
          'provider' => 'users',
          'hash' => false,
      ],
  ],
```

Userモデルの修正

app/Models/Userを下記の通りに修正する。

・「Tymon\JWTAuth\Contracts\JWTSubject」のuse宣言とimplementsとして設定

・「JWTSubject」で定義されているメソッドを定義する。

＊namespaceに注意する。


```PHP
<?php

namespace App\Model;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    /*  省略  */

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.(JWTSubject)
     *
     * @a return mixed
     */
    public function getJWTIdentifier()
    {
        // primary keyを取得
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.(JWTSubject)
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
```

conposer.jsonの修正
→現在の」Laravel8はModelsディレクトリがあるから不要。(変更になる可能性あり・)

Userモデルの位置を変更した為、修正する。

「autoload」の「psr-4」に。下記を記述を追記する。

```Json
"Model\\": "app/Model/"
```


```Json
    "autoload": {
        "psr-4": {
            "App\\": "app/",
            "Model\\": "app/Model/"
        },
        /* 省略 */
    },
```

composer dump-autoloadの実行

```shell-session
$ composer dump-autoload
```



ルーティングの修正

router/api.phpを下記の通りに修正

＊api.phpに設定されたurlは自動的に「api」というパスが割当てられる為、「api」の記載は不要。
＊Laravel8からルーティングの記載」方法が若干変わった。

```PHP
use App\Http\Controllers\Users\AuthController;

Route::get('test', function () {
    return 'api connection test!';
});

Route::group(['prefix' => 'auth'], function () {
    Route::post('login', [AuthController::class, 'login']);
});

Route::group(['prefix' => 'auth', 'middleware' => 'auth:api'], function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('self', [AuthController::class, 'getAuthUser']);
});


```



コントローラーの作成

*ディレクトリ名は随時変更

```shell-session
 $ php artisan make:controller Users/AuthController
```

各CRUD処理のメソッドを予め作成しておきたい場合は`--resource`オプションをつける

```shell-session
 $ php artisan make:controller Users/AuthController --resource
```

内容は下記の通り(コンストラクタとログイン処理のみ抜粋)


```PHP
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Illuminate\Routing\Controller
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }
```

テストユーザーの作成


```shell-session
 $ php artisan make:seeder UsersTableSeeder
```

シーダーファイルの作成

passwordなどはconfigで設定すると良い。


```PHP

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'testuser',
            'email' => 'testuser@example.com',
            'password' => bcrypt('testpassword'),
        ]);
    }
}

```

DatabaseSeederの編集


```PHP

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UserSeeder::class);
        $this->call(UsersTableSeeder::class);
    }
}

```


シーディングの実行

```shell-session
 $ php artisan db:seed
```

ログインリクエストの実行


PostmanなどのAPIクライアントで下記のURLでPOSTリクエストを実行する。

```shell-session
localhost/api/auth/login
```

リクエストボディ

```JSON
{
	"email": "testuser@example.com",
	"password": "testpassword"
}
```

レスポンスボディ

```JSON
{
    "access_token": "ランダム文字列のトークン",
    "token_type": "bearer",
    "expires_in": 3600
}
```

### JetStreamなど不要な機能の削除

機能を自作したい時などはマイグレーションファイルのうち、下記をリネーム等して除外する。

```shell
2014_10_12_200000_add_two_factor_columns_to_users_table.php
2019_08_19_000000_create_failed_jobs_table.php
2019_12_14_000001_create_personal_access_tokens_table.php
```

`app.php`に記載されている下記のServiceProviderをコメントアウトをする。

ServiceProviderで利用している`App\Actions\Fortify`、`App\Actions\Jetstream`も不要になる為該当のServiceProvider含めて削除する。

```php
    App\Providers\FortifyServiceProvider::class,
    App\Providers\JetstreamServiceProvider::class,
```

上記を削除したら`App/Actions`ディレクトリをまとめて削除しても良い。

---
# その他

### テーブル作成(マイグレーションファイル作成)

```shell-session
 $ php artisan make:migration create_test_table
```

### Model作成

```shell-session
 $ php artisan make:model Models/Test
```

`v8`では`Models`ディレクトリがある為、Model名のみを指定すれば良い。

```shell-session
 $ php artisan make:model Test
```

### シーディングファイル作成

```shell-session
 $ php artisan make:seeder TestTableSeeder
```

シーディングが出来ない場合、`composer dump-autoload`をかけると良い。

### ファクトリーファイル作成

```shell-session
 $ php artisan make:factory TestFactory
```

### ポリシーの作成

```shell-session
$ php artisan make:policy TestPolicy
```

「/app/Policies」ディレクトリにファイルが生成される。

### テストコードの作成

```shell-session
$ php artisan make:test SampleTest --unit
```

### ログの設定

※日付ごとにログを出力する方法
`.env`の`LOG_CHANNEL`を下記の通りに設定する。(defaultが`stack`)

```shell-session
# LOG_CHANNEL=stack
LOG_CHANNEL=daily
```

ログ出力の例

```PHP
use Log; // app.configでエイリアスが設定されている

Log::alert('log test');
Log::info(__CLASS__ . '::' . __FUNCTION__ . ' line:' . __LINE__ . ' ' .'log test message.');
```

### ハンドラー(リスナー)の作成

※日付ごとにログを出力する方法
`.env`の`LOG_CHANNEL`を下記の通りに設定する。(defaultが`stack`)

```shell-session
$ php artisan make:listener TestHandler
```

### サービスプロパイダーの作成

```shell-session
$ php artisan make:provider TestServiceProvider
```

### リソースの作成

```shell-session
$ php artisan make:resource Test
```
### コレクションリソースの作成

```shell-session
$ php artisan make:resource Test --collection
or
$ php artisan make:resource TestCollection
```

### ミドルウェアの作成

```shell-session
$ php artisan make:middleware TestMiddleWare
```

### フォームリクエストの作成(バリデーションルール)

```shell-session
$ php artisan make:request TestPostRequest
```

### Excel,CSVファイルの入出力

- Laravel-Excelのインストール

```shell-session
$ composer require maatwebsite/excel

# php8+Laravel9から、「psr/simple-cache」もインストールする必要がある。(上記でエラーが発生するなら下記で対応。)
$ composer require psr/simple-cache:^1.0 maatwebsite/excel
```

- app.phpにサービスプロパイダ(providers)とファサード(aliases)を登録

app.php

```PHP
Maatwebsite\Excel\ExcelServiceProvider::class,

'Excel' => Maatwebsite\Excel\Facades\Excel::class,
```

`Laravel9`から、デフォルトのFacadeは`Facade`クラス内に設定されるようになったらしい。

```PHP
    'aliases' => Facade::defaultAliases()->merge([
        // 'ExampleClass' => App\Example\ExampleClass::class,
        // add
        'Excel' => Maatwebsite\Excel\Facades\Excel::class,
    ])->toArray(),
```

- stubの作成

```shell-session
$ php artisan vendor:publish --provider="Maatwebsite\Excel\ExcelServiceProvider"
```

- エクスポートクラスとインポートクラスの作成

```shell-session
$ php artisan make:export TestExport --model=App\\Models\\Admins
$ php artisan make:import TestImport --model=App\\Models\\Admins
# 子ディレクトリ内に作成する場合
$ php artisan make:export Admins\\TestExport --model=App\\Models\\Admins
$ php artisan make:import Admins\\TestExport --model=App\\Models\\Admins
```

- ファイルダウンロード

```PHP
use Maatwebsite\Excel\Facades\Excel;

return Excel::download(new TestExport($collection), 'filename_' . Carbon::now()->format('YmdHis') . '.csv');
```

### 通知の作成

slack通知の場合は`slack-notification-channel`をインストールする。

```shell-session
$ composer require laravel/slack-notification-channel
```

```shell-session
$ php artisan make:notification TestNotification
```

- Notifiableトレイトの使用

```PHP
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;
}
```

モデルのインスタンスかnotifyトレイトを使い通知を実行する

```PHP
use App\Notifications\TestNotification;

$user->notify(new TestNotification($data));
```

`SlackMessage.php`の実体は下記にあるが、`namespace`が`Illuminate\Notifications\Messages`になっている為左記で参照出来る。

`vendor/laravel/slack-notification-channel/src/Messages/SlackMessage.php`

---

# tinkerの実行

Eloquentモデルの利用方法も記載する。

DB接続の都合上、Dockerコンテナ上で実行する必要がある。

CollectionをToArray()すると、時間のフォーマットが変わる。

```shell-session
$ docker exec -it app-container-name ash
$ php artisan tinker
Psy Shell v0.10.12 (PHP 8.0.15 — cli) by Justin Hileman
>>>
```

```php
$ php artisan tinker
Psy Shell v0.10.12 (PHP 8.0.15 — cli) by Justin Hileman
>>> $adminsModel = new Admins();
[!] Aliasing 'Admins' to 'App\Models\Admins' for this Tinker session.
=> App\Models\Admins {#3961}
>>> $admin = $adminsModel->find(1);
=> App\Models\Admins {#3980
     id: 1,
     name: "admin_name",
     email: "admin.name@example.com",
     email_verified_at: null,
     #password: "xxxxxxxxxxxxxxxx",
     #remember_token: null,
     created_at: "2022-02-04 00:00:00",
     updated_at: "2022-02-04 00:00:00",
     deleted_at: null,
   }
>>>
>>> $admin->roles()->getResults()->toArray();
=> [
     [
       "id" => 1,
       "admin_id" => 1,
       "role_id" => 1,
       "created_at" => "2021-02-04 00:00:00",
       "updated_at" => "2021-02-04 00:00:00",
       "deleted_at" => null,
     ],
   ]
>>>
>>> // test comment
>>> // メソッドの様に`()`をつける必要は無い。
>>> $admin->roles;
=> Illuminate\Database\Eloquent\Collection {#4293
     all: [
       App\Models\AdminsRoles {#4292
         id: 1,
         admin_id: 1,
         role_id: 1,
         created_at: "2021-02-04 00:00:00",
         updated_at: "2021-02-04 00:00:00",
         deleted_at: null,
       },
     ],
   }
>>>
>>> $v->roles->toArray();
=> [
     [
       "id" => 1,
       "admin_id" => 1,
       "role_id" => 1,
       "created_at" => "2021-02-04 00:00:00",
       "updated_at" => "2021-02-04 00:00:00",
       "deleted_at" => null,
     ],
   ]
>>> $admin->adminRoles;
=> null

>>> // hasOneをしたケース
>>> $admin->role
=> App\Models\AdminsRoles {#4006
     id: 1,
     admin_id: 1,
     role_id: 1,
     created_at: "2021-02-04 00:00:00",
     updated_at: "2021-02-04 00:00:00",
     deleted_at: null,
   }

>>> // 関連づけらている側からの確認
>>> $adminsRoleModel = new AdminsRoles();
[!] Aliasing 'AdminsRoles' to 'App\Models\AdminsRoles' for this Tinker session.
=> App\Models\AdminsRoles {#3961}
>>> $adminsRole = $adminsRoleModel->find(1);
=> App\Models\AdminsRoles {#3991
     id: 1,
     admin_id: 1,
     role_id: 1,
     created_at: "2021-02-04 00:00:00",
     updated_at: "2021-02-04 00:00:00",
     deleted_at: null,
   }
>>> $adminsRole->admin
=> App\Models\Admins {#3992
     id: 1,
     name: "admin-name",
     email: "test@example.com",
     email_verified_at: null,
     #password: "xxxxxxxxx",
     #remember_token: null,
     created_at: "2022-02-04 00:00:00",
     updated_at: "2022-02-04 00:00:00",
     deleted_at: null,
   }
>>> $adminsRole->admin->name
=> "admin-name"


>>> // role model
>>> $roleModel = new Roles();
>>> $role = $roleModel->find(1);
=> App\Models\Roles {#4317
     id: 1,
     name: "master",
     code: "master",
     detail: "masterロール",
     created_at: "2021-02-04 00:00:00",
     updated_at: "2021-02-04 00:00:00",
     deleted_at: null,
   }
>>> $role->permissions
=> Illuminate\Database\Eloquent\Collection {#4303
     all: [
       App\Models\RolePermissions {#4302
         id: 1,
         name: "master_create",
         short_name: "create",
         role_id: 1,
         permission_id: 1,
         created_at: "2021-02-04 00:00:00",
         updated_at: "2021-02-04 00:00:00",
         deleted_at: null,
       },
       App\Models\RolePermissions {#4301
         id: 2,
         name: "master_read",
         short_name: "read",
         role_id: 1,
         permission_id: 2,
         created_at: "2021-02-04 00:00:00",
         updated_at: "2021-02-04 00:00:00",
         deleted_at: null,
       },
       App\Models\RolePermissions {#4300
         id: 3,
         name: "master_update",
         short_name: "update",
         role_id: 1,
         permission_id: 3,
         created_at: "2021-02-04 00:00:00",
         updated_at: "2021-02-04 00:00:00",
         deleted_at: null,
       },
       App\Models\RolePermissions {#4299
         id: 4,
         name: "master_delete",
         short_name: "delete",
         role_id: 1,
         permission_id: 4,
         created_at: "2021-02-04 00:00:00",
         updated_at: "2021-02-04 00:00:00",
         deleted_at: null,
       },
     ],
   }

>>> // リレーションから直接where条件を指定出来る。
>>> $role->permissions()->where('id', 2)->get();
=> Illuminate\Database\Eloquent\Collection {#4394
     all: [
       App\Models\RolePermissions {#4393
         id: 2,
         name: "master_read",
         short_name: "read",
         role_id: 1,
         permission_id: 2,
         created_at: "2021-02-04 00:00:00",
         updated_at: "2021-02-04 00:00:00",
         deleted_at: null,
       },
     ],
   }

// 下記の様な形でリレーションからデータを取得出来る。
>> $role->permissions
>>> $role->permissions()->where('permission_id', 2)->get();
>>> $role->permissions()->where('permission_id', 2)->get()->toArray();
>>> $role->permissions()->whereIn('permission_id', [2,4])->get()->toArray();



```

### many to many(多対多)

```php
$ php artisan tinker
Psy Shell v0.10.12 (PHP 8.0.15 — cli) by Justin Hileman
>>> $a = new Admins();
>>> $admin = $a->find(1);
>>> $admin->roles
=> Illuminate\Database\Eloquent\Collection {#3993
     all: [
       App\Models\Roles {#4006
         id: 1,
         name: "master",
         code: "master",
         detail: "masterロール",
         created_at: "2021-02-04 00:00:00",
         updated_at: "2021-02-04 00:00:00",
         deleted_at: null,
         pivot: Illuminate\Database\Eloquent\Relations\Pivot {#4005
           admin_id: 1,
           role_id: 1,
         },
       },
     ],
   }
>>> $r = new Roles();
>>> $role = $r->find(2);
>>> $role->admins
=> Illuminate\Database\Eloquent\Collection {#4003
     all: [
       App\Models\Admins {#4017
         id: 2,
         name: "test2",
         email: "test2@example.com",
         email_verified_at: null,
         #password: "xxxxxxxx",
         #remember_token: null,
         created_at: "2022-02-04 00:00:00",
         updated_at: "2022-02-04 00:00:00",
         deleted_at: null,
         pivot: Illuminate\Database\Eloquent\Relations\Pivot {#4016
           role_id: 2,
           admin_id: 2,
         },
       },
     ],
   }

```

---

# ページネーション

query builderやEloquentモデルから`paginate`メソッドを利用する。

Modelでの利用例

```php
>>> $admins = new Admins();
>>> $admins::paginate(10)->toArray();
>>> $admins::paginate(10)->toJson();

// その他オプション
>>> $admins::paginate(10)->hasPages();
>>> $admins::paginate(10)->items();
>>> $admins::paginate(10)->perPage();
>>> $admins::paginate(10)->count();
>>> $admins::paginate(10)->onFirstPage();
>>> $admins::paginate(10)->getOptions();
```

---

# レプリケーション設定

`database.php`の`connections`の、`mysql`の設定を下記の通り設定する。

```php
    'connections' => [
        ...
        // master/slave設定
        'mysql' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'read' => [
                'host' => [
                    env('DB_SLAVE_HOST', '127.0.0.1'),
                ],
                'database' => env('DB_SLAVE_DATABASE', 'forge'),
                'username' => env('DB_SLAVE_USERNAME'),
                'password' => env('DB_SLAVE_PASSWORD'),
                'port' => env('DB_SLAVE_PORT', '3306'),
            ],
            'write' => [
                'host' => [
                    env('DB_MASTER_HOST', '127.0.0.1'),
                ],
                'database' => env('DB_MASTER_DATABASE', 'forge'),
                'username' => env('DB_MASTER_USERNAME'),
                'password' => env('DB_MASTER_PASSWORD'),
                'port' => env('DB_MASTER_PORT', '3306'),
            ],
            'sticky' => true,
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],
        ...
    ],

```

backendの`.env`のDB設定を下記の通りに修正する。

```conf
DB_CONNECTION=mysql
DB_MASTER_HOST=127.0.0.1
DB_MASTER_PORT=3306
DB_MASTER_DATABASE=laravel
DB_MASTER_USERNAME=root
DB_MASTER_PASSWORD=

DB_SLAVE_HOST="${DB_MASTER_HOST}"
DB_SLAVE_PORT="${DB_MASTER_PORT}"
DB_SLAVE_DATABASE="${DB_MASTER_DATABASE}"
DB_SLAVE_USERNAME="${DB_MASTER_USERNAME}"
DB_SLAVE_PASSWORD="${DB_MASTER_PASSWORD}"
```

---

# ロギング設定

`logging.php`の設定を下記の通りに修正する。


```php
        ...
        /* 'errorlog' => [
            'driver' => 'errorlog',
            'level' => env('LOG_LEVEL', 'debug'),
        ], */

        'accesslog' => [
            'driver' => 'daily',
            'path'   => env('LOG_ACCESS_PATH', storage_path('logs/access.log')),
            'level'  => env('LOG_ACCESS_LEVEL', 'debug'),
        ],

        'errorlog' => [
            'driver' => 'daily',
            'path'   => env('LOG_ERROR_PATH', storage_path('logs/error.log')),
            'level'  => env('LOG_ERROR_LEVEL', 'debug'),
        ],

        'sqllog' => [
            'driver' => 'daily',
            'path'   => env('LOG_SQL_PATH', storage_path('logs/sql.log')),
            'level'  => env('LOG_SQL_LEVEL', 'debug'),
        ],
       ...
    ],
```

`.env`に下記の設定追加する。

```conf
# Log Setting(defalt : storage_path('logs/***.log'))
# LOG_ACCESS_PATH=/usr/local/app/log/access.log
# LOG_ERROR_PATH=/usr/local/app/log/error.log
# LOG_SQL_PATH=/usr/local/app/log/sql.log
APP_LOG_DEFAULT_CHANNEL="error"
LOG_ACCESS_LEVEL=info
LOG_ERROR_LEVEL=error
LOG_SQL_LEVEL=info
```

*アクセスログを適用させる為に、`middleware`として`AccessLog.php`を作成する。(Karnel.phpにも設定を追加する。)
*SQLログを適用させる為に、`ServiceProvider`として`DataBaseQueryServiceProvider.php`を作成する。(app.phpにも設定を追加する。)

```php
    protected $middleware = [
        ...
        \App\Http\Middleware\AccessLog::class,
        ...
    ];
```

```php
    'providers' => [
        ...
        // cusom service provider
        App\Providers\DataBaseQueryServiceProvider::class,
        ...
    ],
```

---

# Redis

.envの`host`には`host.docker.internal`を指定すれば同じくネットワーク内のredisコンテナにアクセス出来る。

`.env`のredis設定は下記の通り

```conf
# REDIS_HOST=host.docker.internal
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
REDIS_DB=0
REDIS_CLIENT=predis
```

## redisコンテナへのアクセス

初期状態ではkeyは何も入っていない。


```shell
$ docker exec -it project_redis_container ash
/data # redis-cli
127.0.0.1:6379> keys *
(empty list or set)

# keys 格納されているkeyの一覧を表示する
# type [key] valueのtypeを返す
# get [key] typeがstringの値を取得する
# del [key] 該当のkeyのデータを削除する
# flushdb 全てのデータを削除する
```

### PHP側でredisにデータを格納する

tinkerなどで下記の通り実行する

```php
# php artisan tinker
Psy Shell v0.10.12 (PHP 8.0.15 — cli) by Justin Hileman
>>> use Illuminate\Support\Facades\Redis;
>>> Redis::set('name', 'Test123');
=> true
```

`redis-cli`側のデータ

```shell
/data # redis-cli
127.0.0.1:6379> keys *
1) "project_name_database_name"
127.0.0.1:6379> type project_name_database_name
string
127.0.0.1:6379> get project_name_database_name
"Test123"


# 削除
127.0.0.1:6379> del project_name_database_name
(integer) 1
127.0.0.1:6379> keys *
(empty list or set)
```

### 複数のkeyを格納するパターン

```php
# php artisan tinker
Psy Shell v0.10.12 (PHP 8.0.15 — cli) by Justin Hileman
>>> use Illuminate\Support\Facades\Redis;
>>> Redis::set('name', 'Test123');
=> true
>>> Redis::set('test', 'xxxxx');
=> true
```

`redis-cli`側のデータ

```shell
/data # redis-cli
127.0.0.1:6379> keys *
1) "project_name_database_test"
2) "project_name_database_name"
127.0.0.1:6379> get project_name_database_test
"xxxxx"

# 選択しているデータベース内の全てのキー
127.0.0.1:6379> flushdb
OK
127.0.0.1:6379> keys *
(empty list or set)

# 全てのデータベース内のキーの削除
127.0.0.1:6379> select 1
OK
127.0.0.1:6379[1]> keys *
(empty list or set)
127.0.0.1:6379[1]> select 0
OK
127.0.0.1:6379> keys *
1) "project_name_database_test"
2) "project_name_database_name"
127.0.0.1:6379> select 2
OK
127.0.0.1:6379[2]> keys *
(empty list or set)
127.0.0.1:6379[2]> flushall
OK
127.0.0.1:6379[2]> select 0
OK
127.0.0.1:6379> keys *
(empty list or set)

```

## セッション管理などでredisを利用する場合

`phpredis`をインストール

```shell
$ composer require predis/predis
```

## Sessionファサードの利用

`phpredis`をインストール

```php
    $test1 = Session::all(); // []
    $test2 = Session::get('testKey'); // null
    $test3 = Session::getId(); // session_id
    $test4 = Session::getName(); // project_name_session
    $test6 = Session::has('testKey'); // false
```

---

# 補足

### Composer パッケージのアップデート

下記のコマンドで`yarn upgrade`と同様の要領でパッケージの更新を掛けられる。

```shell-session
$ composer update
```

### Composer パッケージの削除

下記のコマンドでパッケージの削除が行える。`composer.lock`は更新される為、`composer install`を改めてかける。

```shell-session
$ composer remove packageName

# composer.lockの更新
$ composer install
```

---


## Stripeの利用について

### StripeのPHP用パッケージの追加

```shell-session
$ composer require stripe/stripe-php
```

.envに環境変数としてpublic keyとprivate keyを設定する。(configで参照出来る様にする。)

```shell-session
STRIPE_PUBLIC_KEY=test_public_key
STRIPE_SECRET_KEY=test_private_key
```

### StripeClientのインスタンスの作成とロジックの例

```php
use Stripe\StripeClient;

// StripeClientのパラメーターにSTRIPE_SECRET_KEYを設定する。
$stripe = new StripeClient(Config::get('config.name'));

// customers情報の一覧を取得する場合
$stripe->request('GET', '/v1/customers', $params = [], $options = []);
// 取得数を制限する場合
$stripe->request('GET', '/v1/customers', $params = ['limit' => 3], $options = []);

```


---
### backendのpackage.jsonのアップデート

update対象の確認

```shell-session
$ npm audit
```

fixをかける。

```shell-session
$ npm audit fix
```

上記でアップデートが出来ない場合はマニュアルでアップデートをかける。

`--force`オプションでアップグレードを掛けられる。

```shell-session
$ npm audit fix --force
```

---
