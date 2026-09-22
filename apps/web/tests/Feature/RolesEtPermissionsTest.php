<?php

namespace Tests\Feature;

use App\Enums\Permission;
use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Les droits viennent du rôle, et de rien d'autre.
 */
class RolesEtPermissionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_le_gerant_pilote_le_laboratoire(): void
    {
        $gerant = User::factory()->role(Role::Gerant)->create();

        $this->assertTrue($gerant->peut(Permission::Facturer));
        $this->assertTrue($gerant->peut(Permission::ParametrerLaboratoire));
        $this->assertTrue($gerant->peut(Permission::VoirIndicateurs));

        $this->assertFalse($gerant->peut(Permission::VoirTournee));
        $this->assertFalse($gerant->peut(Permission::AdministrerPlateforme));
    }

    public function test_le_prothesiste_ne_fait_que_valider_ses_etapes(): void
    {
        $prothesiste = User::factory()->role(Role::Prothesiste)->create();

        $this->assertSame(
            [Permission::ValiderEtapeFabrication],
            $prothesiste->permissions()
        );
    }

    public function test_le_livreur_ne_voit_que_sa_tournee(): void
    {
        $livreur = User::factory()->role(Role::Livreur)->create();

        $this->assertSame([Permission::VoirTournee], $livreur->permissions());
    }

    public function test_le_dentiste_commande_et_suit_mais_ne_facture_pas(): void
    {
        $dentiste = User::factory()->role(Role::Dentiste)->create();

        $this->assertTrue($dentiste->peut(Permission::CommanderEnLigne));
        $this->assertTrue($dentiste->peut(Permission::SuivreSesTravaux));
        $this->assertFalse($dentiste->peut(Permission::Facturer));
        $this->assertFalse($dentiste->peut(Permission::ValiderEtapeFabrication));
    }

    public function test_l_administrateur_de_la_plateforme_ne_touche_a_aucune_donnee_metier(): void
    {
        $administrateur = User::factory()->role(Role::AdministrateurPlateforme)->create();

        $this->assertSame(
            [Permission::AdministrerPlateforme],
            $administrateur->permissions()
        );
        $this->assertFalse($administrateur->peut(Permission::Facturer));
        $this->assertFalse($administrateur->peut(Permission::SaisirCommande));
    }

    public function test_un_compte_sans_role_n_a_aucun_droit(): void
    {
        $sansRole = User::factory()->sansRole()->create();

        $this->assertSame([], $sansRole->permissions());

        foreach (Permission::cases() as $permission) {
            $this->assertFalse(
                $sansRole->peut($permission),
                "Un compte sans rôle ne devrait pas pouvoir [{$permission->value}]."
            );
        }
    }

    public function test_les_permissions_sont_aussi_des_capacites_du_framework(): void
    {
        $secretaire = User::factory()->role(Role::Secretaire)->create();

        $this->assertTrue($secretaire->can(Permission::SaisirCommande->value));
        $this->assertFalse($secretaire->can(Permission::Facturer->value));
    }

    public function test_la_double_authentification_est_exigee_des_roles_sensibles(): void
    {
        $this->assertTrue(Role::Gerant->doubleAuthentificationObligatoire());
        $this->assertTrue(Role::AdministrateurPlateforme->doubleAuthentificationObligatoire());

        $this->assertFalse(Role::Prothesiste->doubleAuthentificationObligatoire());
        $this->assertFalse(Role::Secretaire->doubleAuthentificationObligatoire());
        $this->assertFalse(Role::Livreur->doubleAuthentificationObligatoire());
        $this->assertFalse(Role::Dentiste->doubleAuthentificationObligatoire());
    }
}
