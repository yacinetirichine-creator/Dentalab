<?php

namespace App\Support\Navigation;

use App\Enums\Permission;
use App\Models\User;

/**
 * Le menu d'un utilisateur, déduit de ses permissions.
 *
 * Une entrée n'apparaît que si l'utilisateur a le droit d'ouvrir l'écran
 * correspondant. Cacher l'entrée ne suffit pas : chaque route porte aussi sa
 * propre vérification (`can:`). Le menu est un confort, pas une sécurité.
 */
class Menu
{
    /**
     * @return list<array{libelle: string, route: string, url: string}>
     */
    public static function pour(?User $utilisateur): array
    {
        if ($utilisateur === null) {
            return [];
        }

        $entrees = [
            ['libelle' => 'Tableau de bord', 'route' => 'tableau-de-bord', 'permission' => null],
            ['libelle' => 'Nouvelle commande', 'route' => 'commandes.nouvelle', 'permission' => Permission::SaisirCommande],
            ['libelle' => 'Atelier', 'route' => 'atelier', 'permission' => Permission::ValiderEtapeFabrication],
            ['libelle' => 'Ma tournée', 'route' => 'tournee', 'permission' => Permission::VoirTournee],
            ['libelle' => 'Mes travaux', 'route' => 'mes-travaux', 'permission' => Permission::SuivreSesTravaux],
            ['libelle' => 'Facturation', 'route' => 'facturation', 'permission' => Permission::Facturer],
            ['libelle' => 'Paramètres', 'route' => 'parametres', 'permission' => Permission::ParametrerLaboratoire],
        ];

        $visibles = [];

        foreach ($entrees as $entree) {
            $permission = $entree['permission'];

            if ($permission !== null && ! $utilisateur->peut($permission)) {
                continue;
            }

            $visibles[] = [
                'libelle' => $entree['libelle'],
                'route' => $entree['route'],
                'url' => route($entree['route']),
            ];
        }

        return $visibles;
    }
}
