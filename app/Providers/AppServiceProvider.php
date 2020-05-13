<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Schema\Builder;

use Laravel\Passport\Console\ClientCommand;
use Laravel\Passport\Console\InstallCommand;
use Laravel\Passport\Console\KeysCommand;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider{
    /**
     * Register any application services.
     * @return void
     */
    public function register(){
        //
    }

    /**
     * Bootstrap any application services.
     * @return void
     */
    public function boot(){
        Builder::defaultStringLength(191);
        Passport::routes();
        $this->commands([
            InstallCommand::class,
            ClientCommand::class,
            KeysCommand::class,
        ]);
    }
}
