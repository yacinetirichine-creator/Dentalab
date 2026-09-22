<?php

namespace Tests\Feature\Clients;

use App\Enums\Role;
use App\Models\Cabinet;
use App\Models\Laboratoire;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CabinetTest extends TestCase
{
    use RefreshDatabase;

    protected Laboratoire $laboratoire;

    protected User $gerant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->laboratoire = Laboratoire::factory()->create();
        $this->gerant = User::factory()
            ->role(Role::Gerant)
            ->avecDoubleAuthentification()
            ->create(['laboratoire_id' => $this->laboratoire->id]);
    }

    public function test_un_gerant_cree_un_cabinet(): void
    {
        $this->actingAs($this->gerant)
            ->post('/clients', [
                'raison_sociale' => 'Cabinet des Lilas',
                'siret' => '12345678901234',
                'email' => 'contact@lilas.test',
                'ville' => 'Lyon',
                'pays' => 'FR',
                'delai_reglement_jours' => 45,
            ])
            ->assertRedirect();

        $cabinet = Cabinet::withoutGlobalScope('laboratoire')->sole();

        $this->assertSame('Cabinet des Lilas', $cabinet->raison_sociale);
        $this->assertSame(45, $cabinet->delai_reglement_jours);
        // Le laboratoire n'est jamais envoyé par le formulaire : il vient du
        // compte connecté, via le trait AppartientAuLaboratoire.
        $this->assertSame($this->laboratoire->id, $cabinet->laboratoire_id);
    }

    public function test_la_raison_sociale_est_obligatoire(): void
    {
        $this->actingAs($this->gerant)
            ->post('/clients', ['raison_sociale' => '', 'pays' => 'FR'])
            ->assertSessionHasErrors(['raison_sociale' => 'Le champ raison sociale est obligatoire.']);

        $this->assertSame(0, Cabinet::withoutGlobalScope('laboratoire')->count());
    }

    public function test_un_siret_doit_avoir_quatorze_chiffres(): void
    {
        $this->actingAs($this->gerant)
            ->post('/clients', [
                'raison_sociale' => 'Cabinet test',
                'siret' => '123',
                'pays' => 'FR',
                'delai_reglement_jours' => 30,
            ])
            ->assertSessionHasErrors('siret');
    }

    public function test_deux_cabinets_du_meme_laboratoire_ne_partagent_pas_un_siret(): void
    {
        Cabinet::factory()->create([
            'laboratoire_id' => $this->laboratoire->id,
            'siret' => '12345678901234',
        ]);

        $this->actingAs($this->gerant)
            ->post('/clients', [
                'raison_sociale' => 'Doublon',
                'siret' => '12345678901234',
                'pays' => 'FR',
                'delai_reglement_jours' => 30,
            ])
            ->assertSessionHasErrors('siret');
    }

    public function test_deux_laboratoires_peuvent_avoir_le_meme_cabinet_pour_client(): void
    {
        Cabinet::factory()->create(['siret' => '12345678901234']);

        $this->actingAs($this->gerant)
            ->post('/clients', [
                'raison_sociale' => 'Le meme cabinet, vu par un autre labo',
                'siret' => '12345678901234',
                'pays' => 'FR',
                'delai_reglement_jours' => 30,
            ])
            ->assertSessionHasNoErrors();
    }

    public function test_un_gerant_modifie_un_cabinet(): void
    {
        $cabinet = Cabinet::factory()->create(['laboratoire_id' => $this->laboratoire->id]);

        $this->actingAs($this->gerant)
            ->put("/clients/{$cabinet->id}", [
                'raison_sociale' => 'Nouvelle raison sociale',
                'pays' => 'FR',
                'delai_reglement_jours' => 60,
            ])
            ->assertRedirect("/clients/{$cabinet->id}");

        $this->assertSame('Nouvelle raison sociale', $cabinet->fresh()->raison_sociale);
        $this->assertSame(60, $cabinet->fresh()->delai_reglement_jours);
    }

    public function test_archiver_un_cabinet_le_retire_des_listes_sans_le_supprimer(): void
    {
        $cabinet = Cabinet::factory()->create(['laboratoire_id' => $this->laboratoire->id]);

        $this->actingAs($this->gerant)
            ->post("/clients/{$cabinet->id}/archiver")
            ->assertRedirect('/clients');

        $this->assertNotNull($cabinet->fresh()->archive_le);
        $this->assertDatabaseHas('cabinets', ['id' => $cabinet->id]);

        $liste = $this->actingAs($this->gerant)->get('/clients');
        $this->assertSame([], $liste->viewData('page')['props']['cabinets']);

        $archives = $this->actingAs($this->gerant)->get('/clients?archives=1');
        $this->assertCount(1, $archives->viewData('page')['props']['cabinets']);
    }

    public function test_un_cabinet_archive_se_restaure(): void
    {
        $cabinet = Cabinet::factory()->archive()->create(['laboratoire_id' => $this->laboratoire->id]);

        $this->actingAs($this->gerant)
            ->post("/clients/{$cabinet->id}/restaurer")
            ->assertRedirect("/clients/{$cabinet->id}");

        $this->assertNull($cabinet->fresh()->archive_le);
    }

    public function test_un_laboratoire_ne_voit_pas_les_cabinets_d_un_autre(): void
    {
        $cabinetDuConcurrent = Cabinet::factory()->create();

        $this->actingAs($this->gerant)
            ->get("/clients/{$cabinetDuConcurrent->id}")
            ->assertNotFound();

        $this->actingAs($this->gerant)
            ->put("/clients/{$cabinetDuConcurrent->id}", [
                'raison_sociale' => 'Tentative',
                'pays' => 'FR',
                'delai_reglement_jours' => 30,
            ])
            ->assertNotFound();

        $this->actingAs($this->gerant)
            ->post("/clients/{$cabinetDuConcurrent->id}/archiver")
            ->assertNotFound();

        $this->assertNull($cabinetDuConcurrent->fresh()->archive_le);
    }

    public function test_la_liste_ne_montre_que_les_cabinets_du_laboratoire(): void
    {
        Cabinet::factory()->create([
            'laboratoire_id' => $this->laboratoire->id,
            'raison_sociale' => 'Le mien',
        ]);
        Cabinet::factory()->create(['raison_sociale' => 'Celui du concurrent']);

        $liste = $this->actingAs($this->gerant)->get('/clients');

        $raisonsSociales = array_column($liste->viewData('page')['props']['cabinets'], 'raison_sociale');

        $this->assertSame(['Le mien'], $raisonsSociales);
    }
}
