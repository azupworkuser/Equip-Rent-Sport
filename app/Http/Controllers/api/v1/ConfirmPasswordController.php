<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ConfirmsPasswords;

class ConfirmPasswordController extends Controller
{
    use ConfirmsPasswords;

    /**
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
}
