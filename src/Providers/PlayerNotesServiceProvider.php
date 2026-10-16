<?php

namespace Azuriom\Plugin\PlayerNotes\Providers;

use Azuriom\Extensions\Plugin\BasePluginServiceProvider;
use Azuriom\Models\Permission;
use Azuriom\Plugin\PlayerNotes\Models\Note;
use Azuriom\Plugin\PlayerNotes\Policies\NotePolicy;
use Azuriom\Plugin\PlayerNotes\View\PlayerCardComposer;
use Illuminate\Support\Facades\View;

class PlayerNotesServiceProvider extends BasePluginServiceProvider
{
    protected array $policies = [Note::class => NotePolicy::class];

    public function boot(): void
    {
        $this->loadViews();
        $this->loadTranslations();
        $this->loadMigrations();
        $this->registerPolicies();
        $this->registerAdminNavigation();

        $permissions = [];

        foreach (['view', 'create', 'edit', 'delete', 'moderate', 'pin', 'tags'] as $permission) {
            $permissions['player-notes.'.$permission] = 'player-notes::admin.permissions.'.$permission;
        }

        Permission::registerPermissions($permissions);
        View::composer('admin.users.edit', PlayerCardComposer::class);
    }

    protected function adminNavigation(): array
    {
        return [
            'player-notes' => [
                'name' => trans('player-notes::admin.title'),
                'icon' => 'bi bi-journal-text',
                'route' => 'player-notes.admin.index',
                'permission' => 'player-notes.view',
            ],
        ];
    }
}
