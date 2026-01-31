<?php

namespace Anish\TenantMenu;

use Filament\Contracts\Plugin;
use Filament\Panel;

class TenantMenuPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'tenant-menu';
    }

    public function register(Panel $panel): void
    {
        //
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public function navigationBuilder(string $panelId): \Closure
    {
        return app(TenantMenu::class)->builder($panelId);
    }
}
