<?php

namespace App\Providers;

use App\Http\Livewire\ClienteForm;
use App\Services\MikroTikService;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // $this->app->singleton(MikroTikService::class, function ($app) {
        //     return new MikroTikService();
        // }); SE ELIMINO PORQUE AHORA TRAEMOS PARAMETROS DINAMICOS
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        

    }
}
