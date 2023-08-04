<?php

namespace App\Providers;

use App\Http\Test;
use App\Models\Workspace;
use App\Models\User;
use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\Cashier;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(test::class,function ($text) {
          
            return Test::foo($text);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Cashier::useCustomerModel(Workspace::class);
        Cashier::ignoreMigrations();
    }
}
