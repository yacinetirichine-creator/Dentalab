<?php

namespace Tests\Feature\Clients;

use App\Enums\Role;
use App\Models\Cabinet;
use App\Models\Laboratoire;
use App\Models\Praticien;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PraticienTest extends TestCase
{
    use RefreshDatabase;

    protected Laboratoire $laboratoire;

    protected User $gerant;

    protected Cabinet $cabinet;

    protected function setUp(): void
    {
        parent::setUp();

        $this->laboratoire = Laboratoire::factory()->create();
        $this->gerant = User::factory()
            ->role(Role::Gerant)
            ->avecDoubleAuthentification()
            ->create(['laboratoire_id' => $this->laboratoire->id]);
        $this->cabinet = Cabinet::factory()->create(['laboratoire_id' => $this->laboratoire->id]);
    }

    public function test_un_praticien_est_rattache_au_cabinet(): void
    {
        $this->actingAs($this->gerant)
            ->post("/clients/{$this->cabinet->id}/praticiens", [
                'civilite' => 'Dr',
                'prenom' => 'Camille',
                'nom' => 'Martin',
                'numero_rpps' => '12345678901',
            ])
            ->assertRedirect();

        $praticien = Praticien::withoutGlobalScope('laboratoire')->sole();

        $this->assertSame('Martin', $praticien->nom);
        $this->assertSame($this->cabinet->id, $praticien->cabinet_id);
        $this->assertSame($this->laboratoire->id, $praticien->laboratoire_id);
        $this->assertSame('Dr Camille Martin', $praticien->nomComplet());
    }

    public function test_le_nom_est_obligatoire(): void
    {
        $this->actingAs($this->gerant)
            ->post("/clients/{$this->cabinet->id}/praticiens", ['nom' => ''])
            ->assertSessionHasErrors(['nom' => 'Le champ nom est obligatoire.']);
    }

    public function test_le_numero_rpps_fait_onze_chiffres(): void
    {
        $this->actingAs($this->gerant)
            ->post("/clients/{$this->cabinet->id}/praticiens", [
                'nom' => 'Martin',
                'numero_rpps' => '123',
            ])
            ->assertSessionHasErrors('numero_rpps');
    }

    public function test_un_praticien_se_modifie(): void
    {
        $praticien = Praticien::factory()->create([
            'laboratoire_id' => $this->laboratoire->id,
            'cabinet_id' => $this->cabinet->id,
        ]);

        $this->actingAs($this->gerant)
            ->put("/clients/{$this->cabinet->id}/praticiens/{$praticien->id}", ['nom' => 'Nouveau nom'])
            ->assertRedirect();

        $this->assertSame('Nouveau nom', $praticien->fresh()->nom);
    }

    public function test_un_praticien_s_archive_et_se_restaure(): void
    {
        $praticien = Praticien::factory()->create([
            'laboratoire_id' => $this->laboratoire->id,
            'cabinet_id' => $this->cabinet->id,
        ]);

        $this->actingAs($this->gerant)
            ->post("/clients/{$this->cabinet->id}/praticiens/{$praticien->id}/archiver")
            ->assertRedirect();

        $this->assertNotNull($praticien->fresh()->archive_le);
        $this->assertDatabaseHas('praticiens', ['id' => $praticien->id]);

        $this->actingAs($this->gerant)
            ->post("/clients/{$this->cabinet->id}/praticiens/{$praticien->id}/restaurer")
            ->assertRedirect();

        $this->assertNull($praticien->fresh()->archive_le);
    }

    public function test_un_praticien_d_un_autre_cabinet_n_est_pas_modifiable_depuis_cette_fiche(): void
    {
        $autreCabinet = Cabinet::factory()->create(['laboratoire_id' => $this->laboratoire->id]);
        $praticien = Praticien::factory()->create([
            'laboratoire_id' => $this->laboratoire->id,
            'cabinet_id' => $autreCabinet->id,
            'nom' => 'Intact',
        ]);

        $this->actingAs($this->gerant)
            ->put("/clients/{$this->cabinet->id}/praticiens/{$praticien->id}", ['nom' => 'Tentative'])
            ->assertNotFound();

        $this->assertSame('Intact', $praticien->fresh()->nom);
    }

    public function test_un_praticien_d_un_autre_laboratoire_est_introuvable(): void
    {
        $praticienDuConcurrent = Praticien::factory()->create(['nom' => 'Intact']);

        $this->actingAs($this->gerant)
            ->put(
                "/clients/{$this->cabinet->id}/praticiens/{$praticienDuConcurrent->id}",
                ['nom' => 'Tentative']
            )
            ->assertNotFound();

        $this->assertSame('Intact', $praticienDuConcurrent->fresh()->nom);
    }

    public function test_on_ne_peut_pas_rattacher_un_praticien_au_cabinet_d_un_autre_laboratoire(): void
    {
        $cabinetDuConcurrent = Cabinet::factory()->create();

        $this->actingAs($this->gerant)
            ->post("/clients/{$cabinetDuConcurrent->id}/praticiens", ['nom' => 'Martin'])
            ->assertNotFound();

        $this->assertSame(0, Praticien::withoutGlobalScope('laboratoire')->count());
    }
}
