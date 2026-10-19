<?php

namespace Azuriom\Plugin\UserNotes\Providers;

use Azuriom\Extensions\Plugin\BaseRouteServiceProvider;
use Azuriom\Plugin\UserNotes\Controllers\Admin\LegacyRedirectController;
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

        Route::middleware(['admin-access', 'can:user-notes.view', PrivateResponse::class])
            ->get('admin/player-notes/{path?}', [LegacyRedirectController::class, 'redirect'])
            ->where('path', '.*')->name('user-notes.admin.legacy');
    }
}
