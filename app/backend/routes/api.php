<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
});

/*
|--------------------------------------------------------------------------
| Admin
|--------------------------------------------------------------------------
*/

Route::group(['prefix' => 'v1/admin'], function () {
    // no auth
    Route::group(['prefix' => 'auth'], function () {
        Route::post('login', [\App\Http\Controllers\Admins\AuthController::class, 'login'])->name('auth.admin');
    });
    // \App\Http\Controllers\Admins\AdminsController


    // admin auth
    Route::middleware(['middleware' => 'auth:api-admins'])
    ->group(function () {
        Route::group(['prefix' => 'auth'], function () {
            Route::post('logout', [\App\Http\Controllers\Admins\AuthController::class, 'logout']);
            Route::post('refresh', [\App\Http\Controllers\Admins\AuthController::class, 'refresh']);
            Route::post('self', [\App\Http\Controllers\Admins\AuthController::class, 'getAuthUser']);
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
    });
});

/*
|--------------------------------------------------------------------------
| User
|--------------------------------------------------------------------------
*/

Route::group(['prefix' => 'v1'], function () {
    // no auth
    Route::group(['prefix' => 'auth'], function () {
        Route::post('login', [AuthController::class, 'login'])->name('user.auth.login');
    });


    // user auth
    Route::middleware(['middleware' => 'auth:api-users'])
    ->group(function () {
        Route::group(['prefix' => 'auth'], function () {
            Route::post('logout', [\App\Http\Controllers\Users\AuthController::class, 'logout']);
            Route::post('refresh', [\App\Http\Controllers\Users\AuthController::class, 'refresh']);
            Route::post('self', [\App\Http\Controllers\Users\AuthController::class, 'getAuthUser']);
        });
    });
});
