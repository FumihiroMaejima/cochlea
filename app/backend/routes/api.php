<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Config;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/* Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
}); */

Route::get('test', function () {
    return 'api connection test!';
})->name('api.sample.test.route');

/*
|--------------------------------------------------------------------------
| Admin
|--------------------------------------------------------------------------
*/

Route::group(['prefix' => 'v1/admin'], function () {
    // no auth
    Route::group(['prefix' => 'auth'], function () {
        Route::post('login', [\App\Http\Controllers\Admins\AuthController::class, 'login'])->name('auth.admin.login');
    });

    // forgot password
    Route::group(['prefix' => 'admins'], function () {
        Route::post('/password/forgot', [\App\Http\Controllers\Admins\AdminsController::class, 'forgotPassword'])->name('admin.admins.password.forgot');
        Route::post('/password/reset', [\App\Http\Controllers\Admins\AdminsController::class, 'resetPassword'])->name('admin.admins.password.reset');
    });
    // \App\Http\Controllers\Admins\AdminsController

    // admin auth
    Route::middleware(['middleware' => 'customAuth:api-admins'])
    ->group(function () {
        Route::group(['prefix' => 'auth'], function () {
            Route::post('logout', [\App\Http\Controllers\Admins\AuthController::class, 'logout'])->name('auth.admin.logout');
            Route::post('refresh', [\App\Http\Controllers\Admins\AuthController::class, 'refresh'])->name('auth.admin.refresh');
            Route::post('self', [\App\Http\Controllers\Admins\AuthController::class, 'getAuthUser'])->name('auth.admin.self');
        });

        // admins
        Route::group(['prefix' => 'admins'], function () {
            Route::get('/', [\App\Http\Controllers\Admins\AdminsController::class, 'index'])->name('admin.admins.index');
            Route::get('/csv', [\App\Http\Controllers\Admins\AdminsController::class, 'download'])->name('admin.admins.download');
            Route::post('/admin', [\App\Http\Controllers\Admins\AdminsController::class, 'create'])->name('admin.admins.create');
            Route::patch('/admin/{id}', [\App\Http\Controllers\Admins\AdminsController::class, 'update'])->name('admin.admins.update');
            Route::delete('/admin/{id}', [\App\Http\Controllers\Admins\AdminsController::class, 'destroy'])->name('admin.admins.delete');
            Route::patch('/admin/{id}/password', [\App\Http\Controllers\Admins\AdminsController::class, 'updatePassword'])->name('admin.admins.password.update');
        });

        // roles
        Route::group(['prefix' => 'roles'], function () {
            Route::get('/', [\App\Http\Controllers\Admins\RolesController::class, 'index'])->name('admin.roles.index');
            Route::get('/list', [\App\Http\Controllers\Admins\RolesController::class, 'list'])->name('admin.roles.list');
            Route::get('/csv', [\App\Http\Controllers\Admins\RolesController::class, 'download'])->name('admin.roles.download');
            Route::post('/role', [\App\Http\Controllers\Admins\RolesController::class, 'create'])->name('admin.roles.create');
            Route::patch('/role/{id}', [\App\Http\Controllers\Admins\RolesController::class, 'update'])->name('admin.roles.update');
            Route::delete('/role', [\App\Http\Controllers\Admins\RolesController::class, 'destroy'])->name('admin.roles.delete');
        });

        // permissions
        Route::group(['prefix' => 'permissions'], function () {
            Route::get('/list', [\App\Http\Controllers\Admins\PermissionsController::class, 'list'])->name('admin.permissions.list');
        });

        // banners
        Route::group(['prefix' => 'banners'], function () {
            Route::get('/', [\App\Http\Controllers\Admins\BannersController::class, 'index'])->name('admin.banners.index');
            Route::get('/csv', [\App\Http\Controllers\Admins\BannersController::class, 'download'])->name('admin.banners.download.csv');
            Route::post('/banner', [\App\Http\Controllers\Admins\BannersController::class, 'create'])->name('admin.banners.create');
            Route::get('/banner/image/{uuid}', [\App\Http\Controllers\Admins\BannersController::class, 'getImage'])->name('admin.banners.image');
            Route::post('/banner/image/{uuid}', [\App\Http\Controllers\Admins\BannersController::class, 'uploadImage'])->name('admin.banners.uploadImage');
            Route::patch('/banner/{uuid}', [\App\Http\Controllers\Admins\BannersController::class, 'update'])->name('admin.banners.update');
            Route::delete('/banner', [\App\Http\Controllers\Admins\BannersController::class, 'destroy'])->name('admin.banners.delete');
            Route::get('/file/template', [\App\Http\Controllers\Admins\BannersController::class, 'template'])->name('admin.banners.download.template');
            Route::post('/file/template', [\App\Http\Controllers\Admins\BannersController::class, 'uploadTemplate'])->name('admin.banners.upload.template');
        });

        // coins
        Route::group(['prefix' => 'coins'], function () {
            Route::get('/', [\App\Http\Controllers\Admins\CoinsController::class, 'index'])->name('admin.coins.index');
            Route::get('/csv', [\App\Http\Controllers\Admins\CoinsController::class, 'download'])->name('admin.coins.download.csv');
            Route::post('/coin', [\App\Http\Controllers\Admins\CoinsController::class, 'create'])->name('admin.coins.create');
            Route::patch('/coin/{id}', [\App\Http\Controllers\Admins\CoinsController::class, 'update'])->name('admin.coins.update');
            Route::delete('/coin', [\App\Http\Controllers\Admins\CoinsController::class, 'destroy'])->name('admin.coins.delete');
            Route::get('/file/template', [\App\Http\Controllers\Admins\CoinsController::class, 'template'])->name('admin.coins.download.template');
            Route::post('/file/template', [\App\Http\Controllers\Admins\CoinsController::class, 'uploadTemplate'])->name('admin.coins.upload.template');
        });

        // events
        Route::group(['prefix' => 'events'], function () {
            Route::get('/', [\App\Http\Controllers\Admins\EventsController::class, 'index'])->name('admin.events.index');
            Route::get('/csv', [\App\Http\Controllers\Admins\EventsController::class, 'download'])->name('admin.events.download.csv');
            Route::post('/event', [\App\Http\Controllers\Admins\EventsController::class, 'create'])->name('admin.events.create');
            Route::patch('/event/{id}', [\App\Http\Controllers\Admins\EventsController::class, 'update'])->name('admin.events.update');
            Route::delete('/event', [\App\Http\Controllers\Admins\EventsController::class, 'destroy'])->name('admin.events.delete');
            Route::get('/file/template', [\App\Http\Controllers\Admins\EventsController::class, 'template'])->name('admin.events.download.template');
            Route::post('/file/template', [\App\Http\Controllers\Admins\EventsController::class, 'uploadTemplate'])->name('admin.events.upload.template');
        });

        // home contents
        Route::group(['prefix' => 'home'], function () {
            // banner contents
            Route::group(['prefix' => 'banner'], function () {
                Route::group(['prefix' => 'blocks'], function () {
                    Route::get('/csv', [\App\Http\Controllers\Admins\BannerContentsController::class, 'downloadBannerBlocks'])->name('admin.home.banner.blocks.download.csv');
                    Route::get('/file/template', [\App\Http\Controllers\Admins\BannerContentsController::class, 'templateBannerBlocks'])->name('admin.home.banner.blocks.download.template');
                    Route::post('/file/template', [\App\Http\Controllers\Admins\BannerContentsController::class, 'uploadTemplateBannerBlocks'])->name('admin.home.banner.blocks.upload.template');

                    Route::group(['prefix' => 'contents'], function () {
                        Route::get('/csv', [\App\Http\Controllers\Admins\BannerContentsController::class, 'downloadBannerBlockContents'])->name('admin.home.banner.blocks.contents.download.csv');
                        Route::get('/file/template', [\App\Http\Controllers\Admins\BannerContentsController::class, 'templateBannerBlockContents'])->name('admin.home.banner.blocks.contents.download.template');
                        Route::post('/file/template', [\App\Http\Controllers\Admins\BannerContentsController::class, 'uploadTemplateBannerBlockContents'])->name('admin.home.banner.blocks.contents.upload.template');
                    });
                });
            });

            Route::group(['prefix' => 'contents'], function () {
                Route::get('/csv', [\App\Http\Controllers\Admins\HomeContentsController::class, 'downloadHomeContents'])->name('admin.home.contents.download.csv');
                Route::get('/file/template', [\App\Http\Controllers\Admins\HomeContentsController::class, 'templateHomeContents'])->name('admin.home.contents.download.template');
                Route::post('/file/template', [\App\Http\Controllers\Admins\HomeContentsController::class, 'uploadTemplateHomeContents'])->name('admin.home.contents.upload.template');

                Route::group(['prefix' => 'groups'], function () {
                    Route::get('/csv', [\App\Http\Controllers\Admins\HomeContentsController::class, 'downloadHomeContentsGroups'])->name('admin.home.contents.groups.download.csv');
                    Route::get('/file/template', [\App\Http\Controllers\Admins\HomeContentsController::class, 'templateHomeContentsGroups'])->name('admin.home.contents.groups.download.template');
                    Route::post('/file/template', [\App\Http\Controllers\Admins\HomeContentsController::class, 'uploadTemplateHomeContentsGroups'])->name('admin.home.contents.groups.upload.template');
                });
            });
        });

        // informations
        Route::group(['prefix' => 'informations'], function () {
            Route::get('/', [\App\Http\Controllers\Admins\InformationsController::class, 'index'])->name('admin.informations.index');
            Route::get('/csv', [\App\Http\Controllers\Admins\InformationsController::class, 'download'])->name('admin.informations.download.csv');
            Route::post('/information', [\App\Http\Controllers\Admins\InformationsController::class, 'create'])->name('admin.informations.create');
            Route::patch('/information/{id}', [\App\Http\Controllers\Admins\InformationsController::class, 'update'])->name('admin.informations.update');
            Route::delete('/information', [\App\Http\Controllers\Admins\InformationsController::class, 'destroy'])->name('admin.informations.delete');
            Route::get('/file/template', [\App\Http\Controllers\Admins\InformationsController::class, 'template'])->name('admin.informations.download.template');
            Route::post('/file/template', [\App\Http\Controllers\Admins\InformationsController::class, 'uploadTemplate'])->name('admin.informations.upload.template');
        });
    });

    // debug API
    if (Config::get('app.env') !== 'production') {
        Route::group(['prefix' => 'debug'], function () {
            Route::get('test', [\App\Http\Controllers\Admins\AdminDebugController::class, 'test'])->name('admin.debug.test.get');
            Route::get('status', [\App\Http\Controllers\Admins\AdminDebugController::class, 'getDebugStatus'])->name('admin.debug.status.get');
            Route::get('list', [\App\Http\Controllers\Admins\AdminDebugController::class, 'list'])->name('admin.debug.list.get');
            Route::get('image', [\App\Http\Controllers\Admins\AdminDebugController::class, 'getImage'])->name('admin.debug.image.get');
            Route::post('image', [\App\Http\Controllers\Admins\AdminDebugController::class, 'uploadImage'])->name('admin.debug.image.post');
            Route::get('sample-pdf', [\App\Http\Controllers\Admins\AdminDebugController::class, 'getSamplePDF'])->name('admin.debug.samplePdf.get');
        });
    }
});

/*
|--------------------------------------------------------------------------
| User
|--------------------------------------------------------------------------
*/

Route::group(['prefix' => 'v1'], function () {
    // no auth
    Route::group(['prefix' => 'auth'], function () {
        Route::post('login', [\App\Http\Controllers\Users\AuthController::class, 'login'])->name('auth.user.login');
    });

    // coins
    Route::group(['prefix' => 'coins'], function () {
        Route::get('/', [\App\Http\Controllers\Users\CoinsController::class, 'index'])->name('noAuth.coins.index');
    });
    // banners
    Route::group(['prefix' => 'banners'], function () {
        Route::get('/', [\App\Http\Controllers\Users\BannersController::class, 'index'])->name('noAuth.banners.index');
        Route::get('/banner/image/{uuid}', [\App\Http\Controllers\Users\BannersController::class, 'getImage'])->name('noAuth.banners.image');
    });
    // events
    Route::group(['prefix' => 'events'], function () {
        Route::get('/', [\App\Http\Controllers\Users\EventsController::class, 'index'])->name('noAuth.events.index');
    });
    // events
    Route::group(['prefix' => 'home'], function () {
        Route::get('/contents/list', [\App\Http\Controllers\Users\HomeContentsController::class, 'index'])->name('noAuth.home.contents.index');
    });
    // informations
    Route::group(['prefix' => 'informations'], function () {
        Route::get('/', [\App\Http\Controllers\Users\InformationsController::class, 'index'])->name('noAuth.informations.index');
    });

    // user auth
    Route::middleware(['middleware' => 'auth:api-users'])->group(function () {
        Route::group(['prefix' => 'auth'], function () {
            Route::post('logout', [\App\Http\Controllers\Users\AuthController::class, 'logout'])->name('auth.user.logout');
            Route::post('refresh', [\App\Http\Controllers\Users\AuthController::class, 'refresh'])->name('auth.user.refresh');
            Route::post('self', [\App\Http\Controllers\Users\AuthController::class, 'getAuthUser'])->name('auth.user.self');
        });

        // coins
        Route::group(['prefix' => 'coins'], function () {
            // Route::get('/', [\App\Http\Controllers\Users\UserCoinPaymentController::class, 'checkout'])->name('user.coins.index');

            // coin payment
            Route::group(['prefix' => 'payment'], function () {
                Route::get('/checkout', [\App\Http\Controllers\Users\UserCoinPaymentController::class, 'checkout'])->name('user.coins.payment.checkout');
                Route::get('/cancel', [\App\Http\Controllers\Users\UserCoinPaymentController::class, 'cancel'])->name('user.coins.payment.cancel');
                Route::get('complete', [\App\Http\Controllers\Users\UserCoinPaymentController::class, 'complete'])->name('user.coins.payment.complete');
            });

            // coin history
            Route::group(['prefix' => 'history'], function () {
                Route::get('/list', [\App\Http\Controllers\Users\UserCoinHistoryController::class, 'getCoinHistoryList'])->name('user.coins.history.list');
                Route::get('/{uuid}', [\App\Http\Controllers\Users\UserCoinHistoryController::class, 'getCoinHistory'])->name('user.coins.history.uuid');
                Route::get('/pdf/{uuid}', [\App\Http\Controllers\Users\UserCoinHistoryController::class, 'getCoinHistoryPdf'])->name('user.coins.history.pdf.uuid');
            });
        });

        // informations
        Route::group(['prefix' => 'informations'], function () {
            Route::group(['prefix' => 'information'], function () {
                Route::post('/{id}/alreadyRead', [\App\Http\Controllers\Users\InformationsController::class, 'createUserReadInformation'])->name('user.informations.information.read.create');
                Route::delete('/{id}/alreadyRead', [\App\Http\Controllers\Users\InformationsController::class, 'deleteUserReadInformation'])->name('user.informations.information.read.delete');
            });
        });
    });


    // oauth auth
    Route::middleware(['middleware' => 'oauth_api'])->group(function () {
        Route::group(['prefix' => 'oauth'], function () {
            Route::get('github', [\App\Http\Controllers\Admins\SocialLoginController::class, 'redirectToGitHub'])->name('oauth.github.redirectProvider');
            Route::get('github/callback', [\App\Http\Controllers\Admins\SocialLoginController::class, 'callBackOfGitHub'])->name('oauth.github.callBack');
        });
    });

    // debug API
    if (Config::get('app.env') !== 'production') {
        Route::group(['prefix' => 'debug'], function () {
            Route::get('test', [\App\Http\Controllers\Users\DebugController::class, 'test'])->name('user.debug.test.get');
            Route::get('phpinfo', function () {
                phpinfo();
            })->name('user.debug.phpinfo');

            // デバッグステータス
            Route::get('status', [\App\Http\Controllers\Users\DebugController::class, 'getDebugStatus'])->name('user.debug.status.get');

            // stripe決済
            Route::group(['prefix' => 'checkout'], function () {
                Route::get('/', [\App\Http\Controllers\Users\DebugController::class, 'checkout'])->name('user.debug.checkout.index');
                Route::get('cancel', [\App\Http\Controllers\Users\DebugController::class, 'cancelCheckout'])->name('user.debug.checkout.cancel');
                Route::get('complete', [\App\Http\Controllers\Users\DebugController::class, 'completeCheckout'])->name('user.debug.checkout.complete');
            });

            // コイン付与
            Route::post('coins/assign', [\App\Http\Controllers\Users\DebugController::class, 'assignCoins'])->name('user.debug.coins.assign');

            Route::get('random', [\App\Http\Controllers\Users\DebugController::class, 'debugRandomValue'])->name('user.debug.string.random');
            Route::get('emoji', [\App\Http\Controllers\Users\DebugController::class, 'checkIsEmoji'])->name('user.debug.string.emoji');

            // PDF出力
            Route::get('sample-pdf', [\App\Http\Controllers\Users\DebugController::class, 'getSamplePDF'])->name('user.debug.samplePdf.get');
            Route::get('sample-pdf/coinHistory/{uuid}', [\App\Http\Controllers\Users\DebugController::class, 'getSampleCoinHistoryDesignPDF'])->name('user.debug.samplePdf.coinHistory');
            // QRコード出力
            Route::get('sample-qr', [\App\Http\Controllers\Users\DebugController::class, 'getSampleQRCode'])->name('user.debug.sampleQrCode.get');

            // JWT関係
            Route::group(['prefix' => 'jwt'], function () {
                // JWTトークンヘッダーのデコード
                Route::get('header/decode', [\App\Http\Controllers\Users\DebugController::class, 'decodeTokenHeader'])->name('user.debug.jwt.header.decode');
                // JWTトークンペイロードのデコード
                Route::get('payload/decode', [\App\Http\Controllers\Users\DebugController::class, 'decodeTokenPayload'])->name('user.debug.jwt.payload.decode');
            });

            // メールアドレス関係
            Route::group(['prefix' => 'email'], function () {
                // メールアドレスの暗号化
                Route::get('encrypt', [\App\Http\Controllers\Users\DebugController::class, 'encryptMail'])->name('user.debug.email.encrypt');
                // メールアドレスの複合化
                Route::get('decrypt', [\App\Http\Controllers\Users\DebugController::class, 'decryptMail'])->name('user.debug.email.decrypt');
            });

            // 日時関係
            Route::group(['prefix' => 'datetimes'], function () {
                // 日付からタイムスタンプ
                Route::get('timestamp', [\App\Http\Controllers\Users\DebugController::class, 'getTimeStampByDateTime'])->name('user.debug.datetimes.timestamp');
                // タイムスタンプから日付
                Route::get('datetime', [\App\Http\Controllers\Users\DebugController::class, 'getDateTimeByTimeStamp'])->name('user.debug.datetimes.datetime');
            });

            // ログ関係
            Route::group(['prefix' => 'logs'], function () {
                Route::get('dateLog', [\App\Http\Controllers\Users\DebugController::class, 'getDateLog'])->name('user.debug.logs.api');
            });

            // DB関係
            Route::group(['prefix' => 'databases'], function () {
                Route::get('schema', [\App\Http\Controllers\Users\DebugController::class, 'getSchemaList'])->name('user.debug.databases.schema');
                Route::get('schema/size', [\App\Http\Controllers\Users\DebugController::class, 'getSchemaSizeList'])->name('user.debug.databases.schema.size');
                Route::get('table', [\App\Http\Controllers\Users\DebugController::class, 'getTableStatus'])->name('user.debug.databases.table');
                Route::get('table/size', [\App\Http\Controllers\Users\DebugController::class, 'getTableSizeList'])->name('user.debug.databases.table.size');
            });
        });
    }
});
