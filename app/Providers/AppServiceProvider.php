<?php

namespace App\Providers;

use App\Models\User;
use App\Observers\UserObserver;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\Cashier;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    protected array $observers = [
        User::class => UserObserver::class,
    ];

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        JsonResource::withoutWrapping(false);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Password::defaults(function () {
            return Password::min(6)->mixedCase()->numbers()->symbols()->uncompromised();
        });
        Cashier::ignoreMigrations();

        $this->registerObservers();
    }

    public function registerObservers()
    {
        foreach ($this->observers as $model => $observer) {
            $model::observe($observer);
        }
    }
}
