<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Admin\{
    AdminAuthController,
    PageController,
    ContactController,
    NotificationController,
    AdminUserController,
    CompanyController,
    UserController
};


use App\Http\Controllers\Company\{
    CompanyAuthController,
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
Route::controller(AdminAuthController::class)->group(function () {
    Route::get('/', 'index');  // Default landing page
    Route::get('/home', 'index');  // Redirect to the home page
});

// Admin Routes with 'admin' prefix and 'admin.' name
Route::prefix('admin')->name('admin.')->group(function () {

    // Admin Authentication Routes
    Route::controller(AdminAuthController::class)->group(function () {
        Route::get('/', 'index');  // Admin landing page
        Route::get('login', 'login')->name('login');  // Login page
        Route::post('login', 'postLogin')->name('login.post');  // Handle login form submission
        Route::get('forget-password', 'showForgetPasswordForm')->name('forget.password.get');  // Show forget password form
        Route::post('forget-password', 'submitForgetPasswordForm')->name('forget.password.post');  // Handle forget password form submission
        Route::get('reset-password/{token}', 'showResetPasswordForm')->name('reset.password.get');  // Show reset password form
        Route::post('reset-password', 'submitResetPasswordForm')->name('reset.password.post');  // Handle reset password form submission
    });

    // Routes requiring 'admin' middleware
    Route::middleware('admin')->group(function () {

        // Admin Dashboard and Profile Routes
        Route::controller(AdminAuthController::class)->group(function () {
            Route::get('dashboard', 'adminDashboard')->name('dashboard');  // Admin dashboard
            Route::get('change-password', 'changePassword')->name('change.password');  // Change password form
            Route::post('update-password', 'updatePassword')->name('update.password');  // Handle change password form submission
            Route::get('logout', 'logout')->name('logout');  // Logout route
            Route::get('profile', 'adminProfile')->name('profile');  // Admin profile page
            Route::post('profile', 'updateAdminProfile')->name('update.profile');  // Update admin profile
        });

        // Admin User Management Routes
        Route::prefix('company')->name('company.')->controller(CompanyController::class)->group(function () {
            Route::get('/', 'index')->name('index');  // List all Comapny
            Route::get('allcompany', 'getallCompany')->name('allcompany');  // Fetch all Comapny data
            Route::post('store', 'store')->name('store');  // Store Comapny
            Route::post('status', 'companyStatus')->name('status');  // Update Comapny status
            Route::delete('delete/{id}', 'destroy')->name('destroy');  // Delete a Comapny by ID
            Route::get('company/{id}', 'getCompany')->name('get');  // get a Comapny by ID
            Route::get('{id}', 'show')->name('show');  // Show user details by ID
            Route::post('update', 'updateCompany')->name('update');  // Update Comapny
        });


        // Admin User Management Routes
        Route::prefix('users')->name('user.')->controller(UserController::class)->group(function () {
            Route::get('/', 'index')->name('index');  // This defines the route as 'admin.user.index'
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


// Admin Routes with 'admin' prefix and 'admin.' name
Route::prefix('company')->name('company.')->group(function () {

    // Admin Authentication Routes
    Route::controller(CompanyAuthController::class)->group(function () {
        Route::get('/', 'index');  // Admin landing page
        Route::get('login', 'login')->name('login');  // Login page
        Route::post('login', 'postLogin')->name('login.post');  // Handle login form submission
        Route::get('forget-password', 'showForgetPasswordForm')->name('forget.password.get');  // Show forget password form
        Route::post('forget-password', 'submitForgetPasswordForm')->name('forget.password.post');  // Handle forget password form submission
        Route::get('reset-password/{token}', 'showResetPasswordForm')->name('reset.password.get');  // Show reset password form
        Route::post('reset-password', 'submitResetPasswordForm')->name('reset.password.post');  // Handle reset password form submission

        // Routes requiring 'admin' middleware
        Route::middleware('user')->group(function () {

            // Admin Dashboard and Profile Routes
            Route::controller(CompanyAuthController::class)->group(function () {
                Route::get('dashboard', 'companyDashboard')->name('dashboard');  // Admin dashboard
                Route::get('change-password', 'changePassword')->name('change.password');  // Change password form
                Route::post('update-password', 'updatePassword')->name('update.password');  // Handle change password form submission
                Route::get('logout', 'logout')->name('logout');  // Logout route
                Route::get('profile', 'companyProfile')->name('profile');  // Admin profile page
                Route::post('profile', 'updatecompanyProfile')->name('update.profile');  // Update admin profile
            });

        });
    });
});

// Routes for authenticated users
Route::middleware(['auth'])->group(function () {
    // Define routes that require user authentication here
});
