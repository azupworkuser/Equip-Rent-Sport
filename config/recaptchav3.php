<?php

return [
    'origin' => env('RECAPTCHA_ORIGIN', 'https://www.google.com/recaptcha'),
    'sitekey' => env('MIX_RECAPTCHA_SITE_KEY', ''),
    'secret' => env('RECAPTCHA_SECRET_KEY', ''),
    'locale' => env('RECAPTCHA_LOCALE', '')
];
