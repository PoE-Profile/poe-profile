<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //fixing problem for older version of mysql
        //more https://laravel-news.com/laravel-5-4-key-too-long-error
        Schema::defaultStringLength(191);
        if(cache('current_version')){
            config(['app.poe_version' => cache('current_version')]);
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
