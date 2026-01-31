<?php

namespace Anish\TenantMenu;

use Anish\TenantMenu\Menu\MenuDefinition;
use Anish\TenantMenu\Menu\MenuGroupDefinition;
use Anish\TenantMenu\Support\NavigationItemFactory;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;

class TenantMenu
{
    /** @var \Closure|null */
    protected ?\Closure $tenantResolver = null;

    /** @var array<string, \Closure> */
    protected array $definitions = [];

    public function resolveTenantUsing(\Closure $resolver): void
    {
        $this->tenantResolver = $resolver;
    }

    public function define(string $panelId, \Closure $callback): void
    {
        $this->definitions[$panelId] = $callback;
    }

    public function build(string $panelId, NavigationBuilder $builder): NavigationBuilder
    {
        $definition = $this->definitions[$panelId] ?? null;
        if ($definition === null) {
            return $builder;
        }

        $menu = new MenuDefinition;
        app()->call($definition, ['menu' => $menu, 'm' => $menu]);

        $tenant = $this->resolveCurrentTenant();
        $groups = [];

        foreach ($menu->getGroups() as $groupDef) {
            if ($this->groupVisibleForTenant($groupDef, $tenant)) {
                $items = $this->buildGroupItems($groupDef);
                if ($items !== []) {
                    $groups[] = NavigationGroup::make($groupDef->label)
                        ->icon($groupDef->icon)
                        ->collapsible($groupDef->collapsible)
                        ->collapsed($groupDef->collapsed)
                        ->items($items);
                }
            }
        }

        return $builder->groups($groups);
    }

    public function builder(string $panelId): \Closure
    {
        return fn (NavigationBuilder $builder): NavigationBuilder => $this->build($panelId, $builder);
    }

    protected function resolveCurrentTenant(): ?string
    {
        if ($this->tenantResolver === null) {
            return null;
        }

        return ($this->tenantResolver)(app());
    }

    protected function groupVisibleForTenant(MenuGroupDefinition $group, ?string $tenant): bool
    {
        if ($group->tenants === []) {
            return true;
        }
        if ($tenant === null) {
            return false;
        }

        return in_array($tenant, $group->tenants, true);
    }

    /** @return array<NavigationItem> */
    protected function buildGroupItems(MenuGroupDefinition $group): array
    {
        $items = [];
        foreach ($group->items as $entry) {
            if ($entry instanceof NavigationItem) {
                $items[] = $entry;
                continue;
            }
            [$class, $overrides] = $entry;
            $items[] = NavigationItemFactory::fromClass($class, $overrides);
        }

        return $items;
    }
}
