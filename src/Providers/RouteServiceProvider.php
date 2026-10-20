<?php

namespace Azuriom\Plugin\UserNotes\Providers;

use Azuriom\Extensions\Plugin\BaseRouteServiceProvider;
use Azuriom\Plugin\UserNotes\Middleware\PrivateResponse;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends BaseRouteServiceProvider
{
    public function loadRoutes(): void
    {
        Route::middleware(['admin-access', 'can:user-notes.view', PrivateResponse::class])
            ->prefix('admin/user-notes')
            ->name('user-notes.admin.')
            ->group(plugin_path('user-notes/routes/admin.php'));
    }
}
