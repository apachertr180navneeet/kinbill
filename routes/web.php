<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Admin\{
    AdminAuthController,
    CompanyController,
    UserController
};
use App\Http\Controllers\Company\{
    CompanyAuthController,
    VariationController,
    TaxController,
    ItemController,
    VendorController,
    CustomerController
};

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you can register web routes for your application.
| All routes defined here are assigned to the "web" middleware group.
|
*/

// Public Routes
Route::get('/', [AdminAuthController::class, 'index']);  // Default landing page
Route::get('/home', [AdminAuthController::class, 'index']);  // Redirect to the home page

// Admin Routes with 'admin' prefix and 'admin.' name
Route::prefix('admin')->name('admin.')->group(function () {

    // Admin Authentication Routes
    Route::controller(AdminAuthController::class)->group(function () {
        Route::get('login', 'login')->name('login');
        Route::post('login', 'postLogin')->name('login.post');
        Route::get('forget-password', 'showForgetPasswordForm')->name('forget.password.get');
        Route::post('forget-password', 'submitForgetPasswordForm')->name('forget.password.post');
        Route::get('reset-password/{token}', 'showResetPasswordForm')->name('reset.password.get');
        Route::post('reset-password', 'submitResetPasswordForm')->name('reset.password.post');
    });

    // Routes requiring 'admin' middleware
    Route::middleware('admin')->group(function () {

        // Admin Dashboard and Profile Routes
        Route::controller(AdminAuthController::class)->group(function () {
            Route::get('dashboard', 'adminDashboard')->name('dashboard');
            Route::get('change-password', 'changePassword')->name('change.password');
            Route::post('update-password', 'updatePassword')->name('update.password');
            Route::get('logout', 'logout')->name('logout');
            Route::get('profile', 'adminProfile')->name('profile');
            Route::post('profile', 'updateAdminProfile')->name('update.profile');
        });

        // Admin Company Management Routes
        Route::prefix('company')->name('company.')->controller(CompanyController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('allcompany', 'getallCompany')->name('allcompany');
            Route::post('store', 'store')->name('store');
            Route::post('status', 'companyStatus')->name('status');
            Route::delete('delete/{id}', 'destroy')->name('destroy');
            Route::get('company/{id}', 'getCompany')->name('get');
            Route::get('{id}', 'show')->name('show');
            Route::post('update', 'updateCompany')->name('update');
        });

        // Admin User Management Routes
        Route::prefix('users')->name('user.')->controller(UserController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('all', 'getall')->name('alluser');
            Route::post('store', 'store')->name('store');
            Route::post('status', 'status')->name('status');
            Route::delete('delete/{id}', 'destroy')->name('destroy');
            Route::get('company/{id}', 'get')->name('get');
            Route::get('{id}', 'show')->name('show');
            Route::post('update', 'update')->name('update');
        });

    });
});

// Company Routes with 'company' prefix and 'company.' name
Route::prefix('company')->name('company.')->group(function () {

    // Company Authentication Routes
    Route::controller(CompanyAuthController::class)->group(function () {
        Route::get('login', 'login')->name('login');
        Route::post('login', 'postLogin')->name('login.post');
        Route::get('forget-password', 'showForgetPasswordForm')->name('forget.password.get');
        Route::post('forget-password', 'submitForgetPasswordForm')->name('forget.password.post');
        Route::get('reset-password/{token}', 'showResetPasswordForm')->name('reset.password.get');
        Route::post('reset-password', 'submitResetPasswordForm')->name('reset.password.post');
    });

    // Routes requiring 'user' middleware
    Route::middleware('user')->group(function () {

        // Company Dashboard and Profile Routes
        Route::controller(CompanyAuthController::class)->group(function () {
            Route::get('dashboard', 'companyDashboard')->name('dashboard');
            Route::get('change-password', 'changePassword')->name('change.password');
            Route::post('update-password', 'updatePassword')->name('update.password');
            Route::get('logout', 'logout')->name('logout');
            Route::get('profile', 'companyProfile')->name('profile');
            Route::post('profile', 'updatecompanyProfile')->name('update.profile');
        });

        // Resource Management Routes
        foreach (['variation', 'tax', 'item', 'vendor', 'customer'] as $resource) {
            Route::prefix($resource)->name("$resource.")->group(function () use ($resource) {
                $controller = "App\Http\Controllers\Company\\" . ucfirst($resource) . "Controller";
                Route::get('/', [$controller, 'index'])->name('index');
                Route::get('all', [$controller, 'getall'])->name('getall');
                Route::post('store', [$controller, 'store'])->name('store');
                Route::post('status', [$controller, 'status'])->name('status');
                Route::delete('delete/{id}', [$controller, 'destroy'])->name('destroy');
                Route::get('get/{id}', [$controller, 'get'])->name('get');
                Route::post('update', [$controller, 'update'])->name('update');
            });
        }
    });
});

// Routes for authenticated users
Route::middleware(['auth'])->group(function () {
    // Define routes that require user authentication here
});

