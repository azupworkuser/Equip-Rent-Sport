<?php

use App\Http\Middleware\OwnerOnly;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckSubscription;
use Stancl\Tenancy\Features\UserImpersonation;
use App\Http\Controllers\Tenant as Controllers;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

Route::group([
    'middleware' => ['tenant', PreventAccessFromCentralDomains::class], // See the middleware group in Http Kernel
    'as' => 'tenant.',
], function () {
    Route::view('/{path?}', 'tenant.app')->where('path', '^((?!api).)*$')->name('react');
});
