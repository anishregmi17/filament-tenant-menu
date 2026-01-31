<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Context resolver (optional)
    |--------------------------------------------------------------------------
    |
    | Closure that returns the current context key (e.g. role: admin, manager).
    | Used to filter which menu groups/items are visible. Return null when no context.
    | Typically you set this in a service provider via resolveTenantUsing().
    |
    */
    'tenant_resolver' => null,

];
