<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Laboratoire;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Ce que chaque rôle voit, et surtout ce qu'il ne voit pas.
 *
 * Le test parcourt la totalité des écrans protégés pour chaque rôle : un
 * oubli de restriction fait échouer le test, pas seulement un oubli
 * d'autorisation.
 */
class AccesParRoleTest extends TestCase
{
    use RefreshDatabase;

    /** Tous les écrans protégés de l'application. */
    protected const ECRANS = [
        '/clients',
        '/parametres',
        '/facturation',
        '/commandes/nouvelle',
        '/atelier',
        '/tournee',
        '/mes-travaux',
    ];

    /**
     * @return array<string, array{Role, list<string>}>
     */
    public static function rolesEtEcransAutorises(): array
    {
        return [
            'gérant' => [Role::Gerant, [
                '/clients',
                '/parametres',
                '/facturation',
                '/commandes/nouvelle',
                '/atelier',
            ]],
            'prothésiste' => [Role::Prothesiste, [
                '/atelier',
            ]],
            'secrétaire' => [Role::Secretaire, [
                '/commandes/nouvelle',
            ]],
            'livreur' => [Role::Livreur, [
                '/tournee',
            ]],
            'dentiste' => [Role::Dentiste, [
                '/mes-travaux',
            ]],
            'administrateur de la plateforme' => [Role::AdministrateurPlateforme, []],
            'compte sans rôle' => [null, []],
        ];
    }

    /**
     * @param  list<string>  $ecransAutorises
     */
    #[DataProvider('rolesEtEcransAutorises')]
    public function test_chaque_role_n_accede_qu_a_ses_ecrans(?Role $role, array $ecransAutorises): void
    {
        $utilisateur = $this->utilisateurPour($role);

        foreach (self::ECRANS as $ecran) {
            $reponse = $this->actingAs($utilisateur)->get($ecran);

            if (in_array($ecran, $ecransAutorises, strict: true)) {
                $reponse->assertOk("Le rôle devrait accéder à {$ecran}.");
            } else {
                $reponse->assertForbidden("Le rôle ne devrait PAS accéder à {$ecran}.");
            }
        }
    }

    #[DataProvider('rolesEtEcransAutorises')]
    public function test_le_menu_ne_propose_que_les_ecrans_autorises(?Role $role, array $ecransAutorises): void
    {
        $utilisateur = $this->utilisateurPour($role);

        $reponse = $this->actingAs($utilisateur)->get('/tableau-de-bord');
        $reponse->assertOk();

        $urlsDuMenu = array_column($reponse->viewData('page')['props']['menu'], 'url');
        $cheminsDuMenu = array_values(array_filter(
            array_map(fn (string $url) => parse_url($url, PHP_URL_PATH), $urlsDuMenu),
            fn (string $chemin) => $chemin !== '/tableau-de-bord',
        ));

        sort($cheminsDuMenu);
        $attendus = $ecransAutorises;
        sort($attendus);

        $this->assertSame($attendus, $cheminsDuMenu);
    }

    public function test_un_visiteur_non_connecte_est_renvoye_vers_la_connexion(): void
    {
        foreach ([...self::ECRANS, '/tableau-de-bord'] as $ecran) {
            $this->get($ecran)->assertRedirect('/login');
        }
    }

    protected function utilisateurPour(?Role $role): User
    {
        $fabrique = User::factory()
            ->state(['laboratoire_id' => Laboratoire::factory()])
            ->avecDoubleAuthentification();

        return $role === null
            ? $fabrique->sansRole()->create()
            : $fabrique->role($role)->create();
    }
}
