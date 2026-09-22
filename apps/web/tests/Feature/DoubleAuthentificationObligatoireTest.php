<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Laboratoire;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * La double authentification n'est pas une option pour les rôles sensibles.
 */
class DoubleAuthentificationObligatoireTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_gerant_sans_double_authentification_est_renvoye_vers_son_activation(): void
    {
        $gerant = User::factory()
            ->role(Role::Gerant)
            ->state(['laboratoire_id' => Laboratoire::factory()])
            ->create();

        $this->actingAs($gerant)
            ->get('/tableau-de-bord')
            ->assertRedirect('/double-authentification');
    }

    public function test_le_gerant_peut_atteindre_l_ecran_d_activation_sans_boucler(): void
    {
        $gerant = User::factory()
            ->role(Role::Gerant)
            ->state(['laboratoire_id' => Laboratoire::factory()])
            ->create();

        $this->actingAs($gerant)
            ->get('/double-authentification')
            ->assertOk();
    }

    public function test_un_gerant_protege_travaille_normalement(): void
    {
        $gerant = User::factory()
            ->role(Role::Gerant)
            ->avecDoubleAuthentification()
            ->state(['laboratoire_id' => Laboratoire::factory()])
            ->create();

        $this->actingAs($gerant)
            ->get('/tableau-de-bord')
            ->assertOk();
    }

    public function test_l_administrateur_de_la_plateforme_y_est_aussi_soumis(): void
    {
        $administrateur = User::factory()
            ->role(Role::AdministrateurPlateforme)
            ->create();

        $this->actingAs($administrateur)
            ->get('/tableau-de-bord')
            ->assertRedirect('/double-authentification');
    }

    public function test_un_prothesiste_n_y_est_pas_contraint(): void
    {
        $prothesiste = User::factory()
            ->role(Role::Prothesiste)
            ->state(['laboratoire_id' => Laboratoire::factory()])
            ->create();

        $this->actingAs($prothesiste)
            ->get('/tableau-de-bord')
            ->assertOk();
    }

    public function test_la_facturation_reste_fermee_au_gerant_tant_qu_il_ne_l_a_pas_activee(): void
    {
        $gerant = User::factory()
            ->role(Role::Gerant)
            ->state(['laboratoire_id' => Laboratoire::factory()])
            ->create();

        $this->actingAs($gerant)
            ->get('/facturation')
            ->assertRedirect('/double-authentification');
    }
}
