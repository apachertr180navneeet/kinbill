<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Web\{
    HomeController
};
use App\Http\Controllers\Admin\{
    AdminAuthController,
    AdminUserController,
    ContactController,
    NotificationController,
    PageController
};

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::controller(HomeController::class)
    ->group(function () {
        Route::get('/', 'index')->name('/');
        Route::get('/home', 'index')->name('home');
    });

Route::controller(AdminAuthController::class)
    ->prefix('admin')
    ->as('admin.')
    ->group(function () {
        Route::get('/', 'index');
        Route::get('login', 'login')->name('login');
        Route::post('login', 'postLogin')->name('login.post');
        Route::get('forget-password', 'showForgetPasswordForm')->name('forget.password.get');
        Route::post('forget-password', 'submitForgetPasswordForm')->name('forget.password.post');
        Route::get('reset-password/{token}', 'showResetPasswordForm')->name('reset.password.get');
        Route::post('reset-password', 'submitResetPasswordForm')->name('reset.password.post');
        Route::middleware(['admin'])->group(function () {
            Route::get('dashboard', 'adminDashboard')->name('dashboard');
            Route::get('change-password', 'changePassword')->name('change.password');
            Route::post('update-password', 'updatePassword')->name('update.password');
            Route::get('logout', 'logout')->name('logout');
            Route::get('profile', 'adminProfile')->name('profile');
            Route::post('profile', 'updateAdminProfile')->name('update.profile');
        });
    });

Route::middleware(['admin'])
    ->prefix('admin')
    ->as('admin.')
    ->group(function () {
        Route::controller(AdminUserController::class)
            ->as('users.')
            ->group(function () {
                Route::get('users', 'index')->name('index');
                Route::get('users/alluser', 'getallUser')->name('alluser');
                Route::post('users/status', 'userStatus')->name('status');
                Route::get('users/delete/{id}', 'destroy')->name('destroy');
                Route::post('users/{id}', 'show')->name('show');
            });

        Route::controller(ContactController::class)
            ->as('contacts.')
            ->group(function () {
                Route::get('contacts', 'index')->name('index');
                Route::get('contacts/all', 'getallcontact')->name('allcontact');
                Route::post('contacts/delete/{id}', 'destroy')->name('destroy');
            });

        Route::controller(PageController::class)
            ->as('page.')
            ->group(function () {
                Route::get('page/create/{key}', 'create')->name('create');
                Route::put('page/update/{key}', 'update')->name('update');
            });

        Route::controller(NotificationController::class)
            ->as('notifications.')
            ->group(function () {
                Route::get('notifications/index', 'index')->name('index');
                Route::get('notifications/clear', 'clear')->name('clear');
                Route::get('notifications/delete/{id}', 'destroy')->name('destroy');
            });
    });
