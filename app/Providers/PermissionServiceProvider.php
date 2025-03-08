<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\File;

class PermissionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // $this->app->register(PermissionServiceProvider::class);

    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {

        $modelsPath = app_path('Models');

        if (!File::exists($modelsPath)) {
            return;
        }

        $models = collect(File::files($modelsPath))
            ->map(fn($file) => pathinfo($file->getFilename(), PATHINFO_FILENAME))
            ->filter(fn($model) => !in_array($model, ['User', 'Role', 'Permission'])) // Exclude some models if necessary
            ->each(function ($model) {
                $this->createPermissionsForModel($model);
            });
    }

    protected function createPermissionsForModel($model)
    {
        $permissions = [
            "view $model",
            "create $model",
            "edit $model",
            "delete $model"
        ];

        foreach ($permissions as $permission) {
            if (!Permission::where('name', $permission)->exists()) {
                Permission::create(['name' => $permission]);
            }
        }
    }
}
