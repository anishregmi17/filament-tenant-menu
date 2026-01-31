<?php

namespace Anish\TenantMenu\Menu;

use Filament\Navigation\NavigationItem;

class MenuDefinition
{
    /** @var array<MenuGroupDefinition> */
    protected array $groups = [];

    /**
     * Define a navigation group. Use for() to restrict by context/role keys.
     */
    public function group(string $label = '', mixed $icon = null): MenuGroupDefinition
    {
        $def = new MenuGroupDefinition($label, $icon);
        $this->groups[] = $def;

        return $def;
    }

    /**
     * @return array<MenuGroupDefinition>
     */
    public function getGroups(): array
    {
        return $this->groups;
    }
}
