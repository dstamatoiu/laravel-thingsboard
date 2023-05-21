<?php

namespace JalalLinuX\Tntity;

use Illuminate\Support\ServiceProvider;
use JalalLinuX\Tntity\Entities\Device\DeviceApi;

class ThingsboardLaravelServiceProvider extends ServiceProvider
{
    const FACADES = [
        /* abstract => concrete */
        'DeviceApi' => DeviceApi::class,
    ];

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/thingsboard.php', 'thingsboard');
    }

    public function boot(): void
    {
        $this->registerCommands();
        $this->registerPublishing();
        $this->registerFacades();
    }

    protected function registerPublishing(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/thingsboard.php' => config_path('thingsboard.php'),
            ]);
        }
    }

    protected function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                //
            ]);
        }
    }

    public function registerFacades(): void
    {
        /* Register Facades */
        foreach (self::FACADES as $abstract => $concrete) {
            $this->app->bind(config('thingsboard.container.prefix') . "." . config('thingsboard.container.prefix.entity') . ".{$abstract}", $concrete);
        }
    }
}