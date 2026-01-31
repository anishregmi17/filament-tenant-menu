<?php

namespace Anish\TenantMenu;

use Illuminate\Support\ServiceProvider;

class TenantMenuServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(TenantMenu::class, fn (): TenantMenu => new TenantMenu);

        $this->mergeConfigFrom(
            __DIR__.'/../config/tenant-menu.php',
            'tenant-menu'
        );
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/tenant-menu.php' => config_path('tenant-menu.php'),
            ], 'tenant-menu-config');
        }
    }
}
