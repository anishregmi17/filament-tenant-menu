# Tenant Menu

Role-based or context-based Filament sidebar navigation. Resolve the current context (e.g. user role) with **Laravel Auth**, define groups and items once, and avoid repeating `NavigationItem` boilerplate.

## Quick start

1. **Install** the package: `composer require anish/tenant-menu`
2. **Register** a context resolver and menu definition in a **service provider** (e.g. using `auth()->user()->role`).
3. **Use** the menu in your panel: `->navigation(app(TenantMenu::class)->builder('admin'))`

## Installation

```bash
composer require anish/tenant-menu:@dev
```

For a local path package, add the path repository in `composer.json` and run the same command.

Publish config (optional):

```bash
php artisan vendor:publish --tag=tenant-menu-config
```

## Setup

### 1. Resolve current context (e.g. by role using Auth)

In `AppServiceProvider` or a dedicated provider, use Laravel’s `auth()` to return a string key (e.g. role):

```php
use Anish\TenantMenu\TenantMenu;

public function boot(): void
{
    $tenantMenu = app(TenantMenu::class);

    $tenantMenu->resolveTenantUsing(function () {
        $user = auth()->user();
        if (! $user) {
            return null;
        }
        // Example: resolve by role (string on User model)
        return $user->role ?? null; // e.g. 'admin', 'manager', 'user'
    });
}
```

Other options:

```php
// If your User has a role attribute
return auth()->user()?->role ?? null;

// Or using a method
return auth()->user()?->getRoleKey() ?? null;

// Or using Laravel Gates
return auth()->user() && Gate::allows('access-admin') ? 'admin' : 'user';
```

### 2. Define the menu

Define the menu for a panel (e.g. `admin`). Use `group(label, icon)` then `->for([...])` to restrict by context keys, and `->add(Resource::class)` or `->add(Page::class)` with optional overrides.

```php
use Anish\TenantMenu\TenantMenu;
use App\Filament\Pages\Dashboard;
use App\Filament\Resources\UserResource;
use App\Filament\Resources\PostResource;
use App\Filament\Pages\SettingsPage;
use Filament\Support\Icons\Heroicon;

public function boot(): void
{
    $tenantMenu = app(TenantMenu::class);

    // ... resolveTenantUsing(...) as above ...

    $tenantMenu->define('admin', function ($m) {
        $m->group('')->for(['admin', 'manager', 'user'])
            ->add(Dashboard::class);

        $m->group('Content')->icon(Heroicon::OutlinedDocumentText)->for(['admin', 'manager'])
            ->add(PostResource::class);

        $m->group('Settings')->icon(Heroicon::OutlinedCog6Tooth)->for(['admin'])
            ->add(UserResource::class)
            ->add(SettingsPage::class);

        // Optional overrides: url, visible, isActiveWhen, label
        $m->group('Reports')->for(['admin'])
            ->add(SomeReportPage::class, [
                'url' => fn () => SomeReportPage::getUrl(),
                'visible' => fn () => auth()->user()?->can('view_reports') ?? false,
            ]);
    });
}
```

- **Icon:** pass as the second argument: `$m->group('Settings', Heroicon::OutlinedCog6Tooth)`.
- **`->for(['admin'])`:** only users whose resolver returns `'admin'` see that group.
- **Overrides:** `url`, `visible`, `isActiveWhen`, `label` (closure or string).

### 3. Use in the panel

In your panel provider (e.g. `AdminPanelProvider`):

```php
use Anish\TenantMenu\TenantMenu;
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        ->id('admin')
        ->navigation(app(TenantMenu::class)->builder('admin'))
        // ...
}
```

Or with the plugin:

```php
use Anish\TenantMenu\TenantMenuPlugin;

$panel
    ->plugins([TenantMenuPlugin::make(), /* ... */])
    ->navigation(TenantMenuPlugin::make()->navigationBuilder('admin'));
```

## Minimal example

One provider that wires everything:

```php
// e.g. app/Providers/TenantMenuRegistrationServiceProvider.php
use Anish\TenantMenu\TenantMenu;
use App\Filament\Pages\Dashboard;
use Filament\Panel;

public function boot(): void
{
    $menu = app(TenantMenu::class);

    $menu->resolveTenantUsing(fn () => auth()->user()?->role ?? null);

    $menu->define('admin', function ($m) {
        $m->group('')->for(['admin', 'user'])->add(Dashboard::class);
    });
}
```

Panel:

```php
->navigation(app(TenantMenu::class)->builder('admin'))
```

## Overrides

When adding a Resource, Page, or Cluster you can pass overrides as the second argument to `add()`:

| Key            | Type              | Example                                 |
| -------------- | ----------------- | --------------------------------------- |
| `url`          | `Closure\|string` | `fn () => SomeResource::getUrl('page')` |
| `visible`      | `Closure\|bool`   | `fn () => auth()->user()?->can('view')` |
| `isActiveWhen` | `Closure`         | `fn () => request()->routeIs('...')`    |
| `label`        | `string\|Closure` | `'Custom Label'`                        |

## API

- **`resolveTenantUsing(\Closure $resolver)`**  
  Set a closure that returns the current context key (e.g. `'admin'`, `'manager'`) or `null`. Use Laravel Auth inside the closure.

- **`define(string $panelId, \Closure $callback)`**  
  Define the menu for a panel. The closure receives one argument `$m` (MenuDefinition).

- **`builder(string $panelId)`**  
  Returns a closure suitable for `Panel::navigation()`.

- **`$m->group(string $label, $icon = null)`**  
  Starts a navigation group. Then:
  - **`->for(array $keys)`** – context keys that can see this group (e.g. `['admin', 'manager']`).
  - **`->add(string $class, array $overrides = [])`** – add a Resource, Page, or Cluster; optional `url`, `visible`, `isActiveWhen`, `label`.
  - **`->addItem(NavigationItem $item)`** – add a raw Filament NavigationItem.
  - **`->collapsible(bool)`**, **`->collapsed(bool)`**

If a group has no `->for()`, it is visible for any context (including when the resolver returns `null`).

## License

MIT.
