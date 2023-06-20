<?php

use App\Http\Controllers\api\v1\ApiKeyController;
use App\Http\Controllers\api\v1\AssetController;
use App\Http\Controllers\api\v1\CategoryController;
use App\Http\Controllers\api\v1\CustomerController;
use App\Http\Controllers\api\v1\DeductibleController;
use App\Http\Controllers\api\v1\DomainController;
use App\Http\Controllers\api\v1\ForgotPasswordController;
use App\Http\Controllers\api\v1\LoginController;
use App\Http\Controllers\api\v1\OfferController;
use App\Http\Controllers\api\v1\PermissionController;
use App\Http\Controllers\api\v1\ProductAvailabilityController;
use App\Http\Controllers\api\v1\ProductController;
use App\Http\Controllers\api\v1\ProductInventoryController;
use App\Http\Controllers\api\v1\ProductLocationController;
use App\Http\Controllers\api\v1\RegisterController;
use App\Http\Controllers\api\v1\ResetPasswordController;
use App\Http\Controllers\api\v1\RoleController;
use App\Http\Controllers\api\v1\SettingsController;
use App\Http\Controllers\api\v1\UserController;
use App\Http\Controllers\api\v1\UserInvitationController;
use App\Http\Controllers\api\v1\UserSettingsController;
use App\Http\Controllers\api\v1\VerificationController;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByRequestData;

Route::controller(RegisterController::class)->group(function () {
    Route::post('/register', 'step1');
});

Route::post('/login', [LoginController::class, 'login']);
Route::controller(SettingsController::class)->group(function () {
    Route::get('/config', 'getConfig');
});

Route::prefix('/password')->group(function () {
    Route::post('/forgot', [ForgotPasswordController::class, 'sendResetLinkEmail']);
    Route::post('/reset', [ResetPasswordController::class, 'reset'])->name('password.reset');
});

Route::prefix('/email/verify')->controller(VerificationController::class)->group(function () {
    Route::get('/', 'verify')->name('verification.verify');
    Route::post('/resend', 'resend');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [UserController::class, 'show']);
    Route::put('/user/change-password', [UserController::class, 'changePassword']);
    Route::middleware(InitializeTenancyByRequestData::class)->group(function () {
        Route::controller(RegisterController::class)->group(function () {
            Route::put('/register/step-2', 'step2');
        });
        Route::controller(UserSettingsController::class)->group(function () {
            Route::put('/user', 'update');
            Route::post('/upload-profile-image', 'uploadProfileImage');
        });
        Route::controller(DomainController::class)->prefix('domain')->group(function () {
            Route::get('/', 'index');
            Route::get('/{domain}', 'show');
            Route::put('{domain}/profile', 'update');
            Route::put('{domain}/region', 'updateRegionalDetails');
            Route::put('{domain}/schedule', 'updateScheduleDetails');
            Route::post('/store', 'store');
        });
        Route::resource('customers', CustomerController::class);
        Route::apiResource('categories', CategoryController::class);
        Route::apiResource('product', ProductController::class);
        Route::prefix('/product/{product}')->group(function () {
            Route::controller(ProductAvailabilityController::class)->group(function () {
                Route::get('/availability', 'show');
                Route::post('/availability', 'store');
                Route::post('/availability/check', 'checkAvailability');
                Route::post('/availability/hold', 'holdAvailability');
                Route::post('/availability/book', 'book');
                Route::get('/availability/{productAvailability}/slots', 'getSlots');
            });

            Route::apiResource('location', ProductLocationController::class);
            Route::apiResource('product-inventory', ProductInventoryController::class);
        });

        Route::apiResource('asset', AssetController::class);
        Route::apiResource('/api-key', ApiKeyController::class)->middleware('abilities:apikey');
        Route::apiResource('offers', OfferController::class);
        Route::apiResource('/deductible', DeductibleController::class);
        Route::apiResource('/user-invitation', UserInvitationController::class);
        Route::apiResource('/role', RoleController::class);
        Route::apiResource('/permission', PermissionController::class);
        Route::apiResource('/user', UserController::class);
    });
});
