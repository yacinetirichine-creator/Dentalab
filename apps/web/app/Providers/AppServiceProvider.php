<?php

namespace App\Providers;

use App\Support\Tenancy\LaboratoireCourant;
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
        //
    }
}
