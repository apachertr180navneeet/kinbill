<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Admin\{
    AdminAuthController,
    PageController,
    ContactController,
    NotificationController,
    AdminUserController,
    CompanyController
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

        // Contact Management Routes
        // Route::prefix('contacts')->name('contacts.')->controller(ContactController::class)->group(function () {
        //     Route::get('/', 'index')->name('index');  // List all contacts
        //     Route::get('all', 'getallcontact')->name('allcontact');  // Fetch all contact data
        //     Route::delete('delete/{id}', 'destroy')->name('destroy');  // Delete a contact by ID
        // });

        // Page Management Routes
        // Route::prefix('page')->name('page.')->controller(PageController::class)->group(function () {
        //     Route::get('create/{key}', 'create')->name('create');  // Create a page with a specific key
        //     Route::put('update/{key}', 'update')->name('update');  // Update a page with a specific key
        // });

        // Notification Management Routes
        // Route::prefix('notifications')->name('notifications.')->controller(NotificationController::class)->group(function () {
        //     Route::get('index', 'index')->name('index');  // List all notifications
        //     Route::get('clear', 'clear')->name('clear');  // Clear notifications
        //     Route::delete('delete/{id}', 'destroy')->name('destroy');  // Delete a notification by ID
        // });
    });
});

// Routes for authenticated users
Route::middleware(['auth'])->group(function () {
    // Define routes that require user authentication here
});
