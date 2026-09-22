<?php

namespace App\Http\Middleware;

use App\Support\Navigation\Menu;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $utilisateur = $request->user();

        return [
            ...parent::share($request),

            'utilisateur' => $utilisateur === null ? null : [
                'nom' => $utilisateur->name,
                'email' => $utilisateur->email,
                'role' => $utilisateur->role?->value,
                'roleLibelle' => $utilisateur->role?->libelle(),
                'laboratoire' => $utilisateur->laboratoire?->nom,
                'doubleAuthentificationActivee' => $utilisateur->two_factor_confirmed_at !== null,
                'permissions' => array_map(
                    fn ($permission) => $permission->value,
                    $utilisateur->permissions()
                ),
            ],

            'menu' => fn () => Menu::pour($utilisateur),

            'message' => fn () => $request->session()->get('status'),

            // Les chaînes d'interface vivent dans lang/<langue>/interface.php
            // et sont lues côté React par le crochet useTraduction().
            'traductions' => fn () => __('interface'),
        ];
    }
}
