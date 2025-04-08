<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(5)->create()->each(function ($user, $index) {
            if ($index < 2) {
            $user->assignRole('admin');
            } else {
            $user->assignRole('user');
            }
        });

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
        ])->assignRole('admin');

        $models = ['FrameColor', 'Frame', 'Color'];
        $actions = ['view', 'create', 'edit', 'delete'];

        foreach ($models as $model) {
            foreach ($actions as $action) {
            Permission::firstOrCreate(['name' => "$action $model"]);
            }
        }
    }
}
