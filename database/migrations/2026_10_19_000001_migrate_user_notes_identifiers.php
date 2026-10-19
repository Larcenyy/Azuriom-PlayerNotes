<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $this->renameIdentifiers('player-notes', 'user-notes', 'players', 'users');
    }

    public function down(): void
    {
        $this->renameIdentifiers('user-notes', 'player-notes', 'users', 'players');
    }

    private function renameIdentifiers(string $oldId, string $newId, string $oldSection, string $newSection): void
    {
        DB::transaction(function () use ($oldId, $newId, $oldSection, $newSection) {
            foreach (DB::table('permissions')->where('permission', 'like', $oldId.'.%')->get() as $permission) {
                $target = $newId.substr($permission->permission, strlen($oldId));
                $exists = DB::table('permissions')->where('role_id', $permission->role_id)
                    ->where('permission', $target)->exists();

                if ($exists) {
                    DB::table('permissions')->where('id', $permission->id)->delete();
                } else {
                    DB::table('permissions')->where('id', $permission->id)->update(['permission' => $target]);
                }
            }

            foreach (DB::table('navbar_elements')->where('type', 'plugin')->where('value', 'like', $oldId.'.%')->get() as $element) {
                $value = $newId.substr($element->value, strlen($oldId));
                $value = str_replace('.'.$oldSection.'.', '.'.$newSection.'.', $value);
                DB::table('navbar_elements')->where('id', $element->id)->update(['value' => $value]);
            }
        });

        Cache::forget('navbar');
    }
};
