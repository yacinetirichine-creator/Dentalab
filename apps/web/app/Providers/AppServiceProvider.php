<?php

namespace App\Providers;

use App\Enums\Permission;
use App\Models\User;
use App\Support\Tenancy\LaboratoireCourant;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // « scoped » plutôt que « singleton » : l'instance est remise à zéro
        // entre deux requêtes, y compris sous Octane. Un laboratoire ne doit
        // jamais fuiter d'une requête à la suivante.
        $this->app->scoped(LaboratoireCourant::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Chaque permission devient une capacité utilisable dans les routes
        // (`can:facturer`), dans le code (`$utilisateur->can(...)`) et dans
        // les vues. Le rôle seul décide (voir App\Enums\Role::permissions()).
        foreach (Permission::cases() as $permission) {
            Gate::define(
                $permission->value,
                fn (User $utilisateur) => $utilisateur->peut($permission)
            );
        }
    }
}
