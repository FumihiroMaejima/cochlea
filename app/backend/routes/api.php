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
})->name('api.test.route');

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
    // \App\Http\Controllers\Admins\AdminsController

    // admin auth
    Route::middleware(['middleware' => 'auth:api-admins'])
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

        // coins
        Route::group(['prefix' => 'coins'], function () {
            Route::get('/', [\App\Http\Controllers\Admins\CoinsController::class, 'index'])->name('admin.coins.index');
            Route::get('/csv', [\App\Http\Controllers\Admins\CoinsController::class, 'download'])->name('admin.coins.download');
            Route::post('/coin', [\App\Http\Controllers\Admins\CoinsController::class, 'create'])->name('admin.coins.create');
            Route::patch('/coin/{id}', [\App\Http\Controllers\Admins\CoinsController::class, 'update'])->name('admin.coins.update');
            Route::delete('/coin', [\App\Http\Controllers\Admins\CoinsController::class, 'destroy'])->name('admin.coins.delete');
        });
    });

    // debug API
    if (Config::get('app.env') !== 'production') {
        Route::group(['prefix' => 'debug'], function () {
            Route::get('test', [\App\Http\Controllers\Admins\AdminDebugController::class, 'test'])->name('admin.debug.test.get');
            Route::get('list', [\App\Http\Controllers\Admins\AdminDebugController::class, 'list'])->name('admin.debug.list.get');
            Route::get('image', [\App\Http\Controllers\Admins\AdminDebugController::class, 'getImage'])->name('admin.debug.image.get');
            Route::post('image', [\App\Http\Controllers\Admins\AdminDebugController::class, 'uploadImage'])->name('admin.debug.image.post');
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
                Route::get('/', [\App\Http\Controllers\Users\UserCoinPaymentController::class, 'checkout'])->name('user.coins.payment.index');
                Route::get('/cancel', [\App\Http\Controllers\Users\UserCoinPaymentController::class, 'cancel'])->name('user.coins.payment.cancel');
                Route::get('complete', [\App\Http\Controllers\Users\UserCoinPaymentController::class, 'complete'])->name('user.coins.payment.complete');
            });
        });
    });

    // debug API
    if (Config::get('app.env') !== 'production') {
        Route::group(['prefix' => 'debug'], function () {
            Route::get('test', [\App\Http\Controllers\Users\DebugController::class, 'test'])->name('user.debug.test.get');

            // stripe決済
            Route::group(['prefix' => 'checkout'], function () {
                Route::get('/', [\App\Http\Controllers\Users\DebugController::class, 'checkout'])->name('user.debug.checkout.index');
                Route::get('cancel', [\App\Http\Controllers\Users\DebugController::class, 'cancelCheckout'])->name('user.debug.checkout.cancel');
                Route::get('complete', [\App\Http\Controllers\Users\DebugController::class, 'completeCheckout'])->name('user.debug.checkout.complete');
            });
        });
    }
});
