<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Laboratoire;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConnexionTest extends TestCase
{
    use RefreshDatabase;

    public function test_l_ecran_de_connexion_s_affiche(): void
    {
        $this->withoutVite()
            ->get('/login')
            ->assertOk();
    }

    public function test_un_utilisateur_se_connecte_avec_les_bons_identifiants(): void
    {
        $utilisateur = User::factory()
            ->role(Role::Prothesiste)
            ->create(['email' => 'technicien@labo.test']);

        $this->post('/login', [
            'email' => 'technicien@labo.test',
            'password' => 'password',
        ])->assertRedirect('/tableau-de-bord');

        $this->assertAuthenticatedAs($utilisateur);
    }

    public function test_un_mauvais_mot_de_passe_ne_connecte_personne(): void
    {
        User::factory()->create(['email' => 'technicien@labo.test']);

        $this->post('/login', [
            'email' => 'technicien@labo.test',
            'password' => 'ce-n-est-pas-le-bon',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_la_deconnexion_ferme_la_session(): void
    {
        $utilisateur = User::factory()->role(Role::Prothesiste)->create();

        $this->actingAs($utilisateur)->post('/logout');

        $this->assertGuest();
    }

    public function test_un_compte_protege_passe_par_la_double_authentification_avant_d_etre_connecte(): void
    {
        User::factory()
            ->role(Role::Gerant)
            ->avecDoubleAuthentification()
            ->state(['laboratoire_id' => Laboratoire::factory()])
            ->create(['email' => 'gerant@labo.test']);

        $this->post('/login', [
            'email' => 'gerant@labo.test',
            'password' => 'password',
        ])->assertRedirect('/two-factor-challenge');

        // Le mot de passe seul ne suffit pas : personne n'est encore connecté.
        $this->assertGuest();
    }
}
