<?php

namespace Azuriom\Plugin\PlayerNotes\Providers;

use Azuriom\Extensions\Plugin\BaseRouteServiceProvider;
use Azuriom\Plugin\PlayerNotes\Middleware\PrivateResponse;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends BaseRouteServiceProvider
{
    public function loadRoutes(): void
    {
        Route::middleware(['admin-access', 'can:player-notes.view', PrivateResponse::class])
            ->prefix('admin/player-notes')
            ->name('player-notes.admin.')
            ->group(plugin_path('player-notes/routes/admin.php'));
    }
}
