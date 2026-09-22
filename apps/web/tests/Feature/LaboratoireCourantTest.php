<?php

namespace Tests\Feature;

use App\Models\Laboratoire;
use App\Models\User;
use App\Support\Tenancy\LaboratoireCourant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * Le laboratoire courant vient du compte connecté, jamais de la requête.
 */
class LaboratoireCourantTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Route::middleware('web')->get('/_test/laboratoire-courant', function () {
            return response()->json([
                'laboratoire_id' => app(LaboratoireCourant::class)->id(),
            ]);
        });
    }

    public function test_le_laboratoire_courant_vient_de_l_utilisateur_connecte(): void
    {
        $laboratoire = Laboratoire::factory()->create();
        $utilisateur = User::factory()->create(['laboratoire_id' => $laboratoire->id]);

        $this->actingAs($utilisateur)
            ->get('/_test/laboratoire-courant')
            ->assertOk()
            ->assertExactJson(['laboratoire_id' => $laboratoire->id]);
    }

    public function test_un_visiteur_non_connecte_n_a_aucun_laboratoire_courant(): void
    {
        $this->get('/_test/laboratoire-courant')
            ->assertOk()
            ->assertExactJson(['laboratoire_id' => null]);
    }

    public function test_l_administrateur_de_la_plateforme_n_a_aucun_laboratoire(): void
    {
        $administrateur = User::factory()->create(['laboratoire_id' => null]);

        $this->actingAs($administrateur)
            ->get('/_test/laboratoire-courant')
            ->assertOk()
            ->assertExactJson(['laboratoire_id' => null]);
    }

    public function test_un_parametre_de_requete_ne_peut_pas_changer_de_laboratoire(): void
    {
        $sien = Laboratoire::factory()->create();
        $celuiDuConcurrent = Laboratoire::factory()->create();
        $utilisateur = User::factory()->create(['laboratoire_id' => $sien->id]);

        $this->actingAs($utilisateur)
            ->get('/_test/laboratoire-courant?laboratoire_id='.$celuiDuConcurrent->id)
            ->assertOk()
            ->assertExactJson(['laboratoire_id' => $sien->id]);
    }
}
