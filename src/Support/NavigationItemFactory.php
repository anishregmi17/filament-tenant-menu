<?php

namespace Anish\TenantMenu\Support;

use Filament\Navigation\NavigationItem;

use function Filament\Support\original_request;

class NavigationItemFactory
{
    /**
     * Build a NavigationItem from a Filament navigatable class (Resource, Page, or Cluster).
     *
     * @param  class-string  $class  Resource, Page, or Cluster class
     * @param  array{url?: \Closure|string|null, visible?: \Closure|bool, isActiveWhen?: \Closure|null, label?: string|\Closure}  $overrides
     * @return NavigationItem
     */
    public static function fromClass(string $class, array $overrides = []): NavigationItem
    {
        $label = $overrides['label'] ?? null;
        if ($label === null && method_exists($class, 'getNavigationLabel')) {
            $label = $class::getNavigationLabel();
        }
        if ($label === null) {
            $label = str(class_basename($class))->kebab()->replace('-', ' ')->title()->toString();
        }

        $item = NavigationItem::make(is_string($label) ? $label : $label);

        if (method_exists($class, 'getNavigationIcon')) {
            $icon = $class::getNavigationIcon();
            if ($icon !== null) {
                $item->icon($icon);
            }
        }

        $url = $overrides['url'] ?? null;
        if ($url === null) {
            if (method_exists($class, 'getNavigationUrl')) {
                $url = fn (): string => $class::getNavigationUrl();
            } elseif (method_exists($class, 'getUrl')) {
                $url = fn (): string => $class::getUrl();
            }
        }
        if ($url !== null) {
            $item->url(is_callable($url) ? $url : fn (): string => (string) $url);
        }

        $visible = $overrides['visible'] ?? null;
        if ($visible === null && method_exists($class, 'canAccess')) {
            $visible = fn (): bool => $class::canAccess();
        }
        if ($visible !== null) {
            $item->visible($visible);
        }

        $isActiveWhen = $overrides['isActiveWhen'] ?? null;
        if ($isActiveWhen === null && method_exists($class, 'getNavigationItemActiveRoutePattern')) {
            $pattern = $class::getNavigationItemActiveRoutePattern();
            $isActiveWhen = fn (): bool => original_request()->routeIs($pattern);
        }
        if ($isActiveWhen !== null) {
            $item->isActiveWhen($isActiveWhen);
        }

        if (method_exists($class, 'getNavigationBadge')) {
            $badge = $class::getNavigationBadge();
            if ($badge !== null) {
                $color = method_exists($class, 'getNavigationBadgeColor') ? $class::getNavigationBadgeColor() : null;
                $item->badge($badge, $color);
            }
        }

        if (method_exists($class, 'getNavigationBadgeTooltip')) {
            $tooltip = $class::getNavigationBadgeTooltip();
            if ($tooltip !== null) {
                $item->badgeTooltip($tooltip);
            }
        }

        if (method_exists($class, 'getNavigationSort')) {
            $sort = $class::getNavigationSort();
            if ($sort !== null) {
                $item->sort($sort);
            }
        }

        return $item;
    }
}
