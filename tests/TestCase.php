<?php

namespace Azuriom\Plugin\PlayerNotes\Tests;

use Azuriom\Models\Role;
use Azuriom\Models\User;
use Azuriom\Plugin\PlayerNotes\Providers\PlayerNotesServiceProvider;
use Azuriom\Plugin\PlayerNotes\Providers\RouteServiceProvider;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

abstract class TestCase extends BaseTestCase
{
    public function createApplication()
    {
        $app = require dirname(__DIR__, 3).'/bootstrap/app.php';
        $app->make(Kernel::class)->bootstrap();

        // Never run plugin tests against an existing application's database.
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite.database', ':memory:');
        $app['config']->set('database.connections.sqlite.url', null);
        $app['config']->set('app.key', 'base64:'.base64_encode(str_repeat('p', 32)));
        $app['config']->set('app.debug', false);
        $app['config']->set('cache.default', 'array');
        $app['config']->set('session.driver', 'array');
        $app['config']->set('app.locale', 'en');
        DB::purge('sqlite');

        $plugin = (object) ['id' => 'player-notes'];

        foreach ([PlayerNotesServiceProvider::class, RouteServiceProvider::class] as $class) {
            $provider = new $class($app);
            $provider->bindPlugin($plugin);
            $app->register($provider, true);
        }

        $app['router']->getRoutes()->refreshNameLookups();
        $app['router']->getRoutes()->refreshActionLookups();

        return $app;
    }

    protected function setUp(): void
    {
        parent::setUp();

        Http::preventStrayRequests();
        Http::fake(['*' => Http::response(['plugins' => [], 'themes' => [], 'update' => null, 'alerts' => []])]);
        $this->artisan('migrate', ['--force' => true])->assertSuccessful();
        Cache::put('updates_counts', ['pluginsUpdates' => 0, 'themesUpdates' => 0], 3600);
        Cache::put('updates', ['plugins' => [], 'themes' => [], 'update' => null, 'alerts' => []], 3600);
    }

    protected function staff(array $permissions = [], bool $admin = false): User
    {
        $role = Role::factory()->create(['is_admin' => $admin]);
        $role->syncPermissions(['admin.access', ...$permissions]);

        return User::factory()->create(['role_id' => $role->id]);
    }

    protected function player(): User
    {
        return User::factory()->create(['role_id' => Role::factory()->create(['is_admin' => false])->id]);
    }
}
