<?php

namespace Azuriom\Plugin\UserNotes\Providers;

use Azuriom\Extensions\Plugin\BasePluginServiceProvider;
use Azuriom\Models\Permission;
use Azuriom\Plugin\UserNotes\Models\Note;
use Azuriom\Plugin\UserNotes\Policies\NotePolicy;
use Azuriom\Plugin\UserNotes\View\UserCardComposer;
use Illuminate\Support\Facades\View;

class UserNotesServiceProvider extends BasePluginServiceProvider
{
    protected array $policies = [Note::class => NotePolicy::class];

    public function boot(): void
    {
        $this->loadViews();
        $this->loadTranslations();
        $this->loadMigrations();
        $this->registerPolicies();
        $this->registerRouteDescriptions();
        $this->registerAdminNavigation();

        $permissions = [];

        foreach (['view', 'create', 'edit', 'delete', 'moderate', 'pin', 'tags'] as $permission) {
            $permissions['user-notes.'.$permission] = 'user-notes::admin.permissions.'.$permission;
        }

        Permission::registerPermissions($permissions);
        View::composer('admin.users.edit', UserCardComposer::class);
    }

    protected function routeDescriptions(): array
    {
        return [
            'user-notes.admin.index' => 'user-notes::admin.title',
        ];
    }

    protected function adminNavigation(): array
    {
        return [
            'user-notes' => [
                'name' => trans('user-notes::admin.title'),
                'icon' => 'bi bi-journal-text',
                'route' => 'user-notes.admin.index',
                'permission' => 'user-notes.view',
            ],
        ];
    }
}
