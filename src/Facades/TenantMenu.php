<?php

namespace Anish\TenantMenu\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void resolveTenantUsing(\Closure $resolver)
 * @method static void define(string $panelId, \Closure $callback)
 * @method static \Filament\Navigation\NavigationBuilder build(string $panelId, \Filament\Navigation\NavigationBuilder $builder)
 * @method static \Closure builder(string $panelId)
 *
 * @see \Anish\TenantMenu\TenantMenu
 */
class TenantMenu extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Anish\TenantMenu\TenantMenu::class;
    }
}
