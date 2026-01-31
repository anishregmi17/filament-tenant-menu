<?php

namespace Anish\TenantMenu\Menu;

use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;

class MenuGroupDefinition
{
    /** @var array<string> */
    public array $tenants = [];

    /** @var array<array{0: class-string, 1?: array}|NavigationItem> */
    public array $items = [];

    public function __construct(
        public ?string $label = null,
        public mixed $icon = null,
        public bool $collapsible = true,
        public bool $collapsed = false,
    ) {}

    /**
     * Context/role keys that can see this group (e.g. ['admin', 'manager']).
     *
     * @param  array<string>  $tenants
     */
    public function for(array $tenants): static
    {
        $this->tenants = $tenants;

        return $this;
    }

    /**
     * Add a Filament navigatable class (Resource, Page, or Cluster).
     *
     * @param  class-string  $class
     * @param  array{url?: \Closure|string|null, visible?: \Closure|bool, isActiveWhen?: \Closure|null, label?: string|\Closure}  $overrides
     */
    public function add(string $class, array $overrides = []): static
    {
        $this->items[] = [$class, $overrides];

        return $this;
    }

    /**
     * Add a raw NavigationItem.
     */
    public function addItem(NavigationItem $item): static
    {
        $this->items[] = $item;

        return $this;
    }

    public function collapsible(bool $value = true): static
    {
        $this->collapsible = $value;

        return $this;
    }

    public function collapsed(bool $value = true): static
    {
        $this->collapsed = $value;

        return $this;
    }
}
