<?php

namespace PresetLocalGenerate;

use Illuminate\Foundation\Console\PresetCommand;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        PresetCommand::macro('local-generate', function($command) {
           Preset::install($command);
        });
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
