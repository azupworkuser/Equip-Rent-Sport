<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

class SettingsController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getConfig()
    {
        return response()->json(
            [
                "languages" => getAllLanguages(),
                "timezones" => getAllTimezones(),
                "currencies" => getAllCurrencies(),
                "countries" => getAllCountryDetails()
            ],
            Response::HTTP_OK
        );
    }
}
