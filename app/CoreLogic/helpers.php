<?php

use App\CoreLogic\Enum\Deductible\DeductibleCategoryEnum;
use App\CoreLogic\Enum\Deductible\DeductibleTypeEnum;
use PragmaRX\Countries\Package\Countries;

if (!function_exists('getAllCurrencies')) {
    function getAllCurrencies()
    {
        $countries = new Countries();
        $currencyDataSource = $countries->currencies()->toArray();
        foreach ($currencyDataSource as $key => $currency) {
            $currencyList[] = [
                "name" => $currency['name'],
                "code" => $currency['iso']['code'],
                "symbol" => $currency['units']['major']['symbol']
            ];
        }
        return $currencyList;
    }
}

if (!function_exists('getAllLanguages')) {
    function getAllLanguages()
    {
        $countries = new Countries();
        $languageDataSource = $countries->all()->pluck('languages')->toArray();
        foreach ($languageDataSource as $key => $multiLanguage) {
            if (is_array(($multiLanguage))) {
                foreach ($multiLanguage as $code => $language) {
                    $languageList[] = [
                        "name" => $language,
                        "code" => $code
                    ];
                }
            }
        }
        return collect($languageList)->unique("name");
    }
}

if (!function_exists('getAllTimezones')) {
    function getAllTimezones()
    {
        $countries = new Countries();
        $collection = $countries->all()->hydrate('timezones')->pluck("timezones");
        $timezones = $collection->mapWithKeys(function ($timezones) {
            $zones = [];
            foreach ($timezones as $timezone) {
                $zones[$timezone['zone_id']] = $timezone;
            }
            return $zones;
        });
        $timezoneList[] = collect($timezones)->map(function ($item, $key) {
            return $item->zone_name;
        });
        return $timezoneList;
    }
}

if (!function_exists('getAllCountryDetails')) {
    function getAllCountryDetails()
    {
        $countries = new Countries();
        return $countries
            ->all()
            ->map(function ($country) use ($countries) {
                $countryName = $country->name->common;
                $countryCode = $country->cca2;
                $dialCode = $countries->where('cca3', $country->cca3)->pluck('calling_codes')->first()->first() ?? null;
                $currency = $countries->where('cca3', $country->cca3)->pluck('currencies')->first()->first() ?? null;
                $language = $countries->where('cca3', $country->cca3)->pluck('languages')->first()->first() ?? null;
                $timezone = $countries->where('cca3', $country->cca3)->first()->hydrate('timezones')->timezones->first()->zone_name ?? null;
                $states = $countries->where('cca3', $country->cca3)->first()->hydrateStates()->states->pluck('name', 'postal')->toArray();
                return [
                    "name" => $countryName,
                    "code" => $countryCode,
                    "dialCode" => $dialCode,
                    "currency" => $currency,
                    "language" => $language,
                    "timezone" => $timezone,
                    "states" => $states
                ];
            });
    }
}

if (!function_exists('getAllDeductibleCategories')) {
    function getAllDeductibleCategories()
    {
        return DeductibleTypeEnum::values();
    }
}

if (!function_exists('getAllDeductibleType')) {
    function getAllDeductibleType()
    {
        return DeductibleCategoryEnum::values();
    }
}

if (!function_exists('currentSubdomain')) {
    function currentSubdomain()
    {
        return request()->header('X-Subdomain');
    }
}
