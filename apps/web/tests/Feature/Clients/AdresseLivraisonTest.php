<?php

namespace Tests\Feature\Clients;

use App\Enums\Role;
use App\Models\AdresseLivraison;
use App\Models\Cabinet;
use App\Models\Laboratoire;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdresseLivraisonTest extends TestCase
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

    /**
     * @param  array<string, mixed>  $remplacements
     * @return array<string, mixed>
     */
    protected function adresseValide(array $remplacements = []): array
    {
        return array_merge([
            'libelle' => 'Cabinet principal',
            'adresse_ligne_1' => '12 rue des Lilas',
            'code_postal' => '69003',
            'ville' => 'Lyon',
            'pays' => 'FR',
        ], $remplacements);
    }

    public function test_une_adresse_est_ajoutee_au_cabinet(): void
    {
        $this->actingAs($this->gerant)
            ->post("/clients/{$this->cabinet->id}/adresses", $this->adresseValide())
            ->assertRedirect();

        $adresse = AdresseLivraison::withoutGlobalScope('laboratoire')->sole();

        $this->assertSame('Cabinet principal', $adresse->libelle);
        $this->assertSame($this->cabinet->id, $adresse->cabinet_id);
        $this->assertSame($this->laboratoire->id, $adresse->laboratoire_id);
    }

    public function test_l_adresse_et_la_ville_sont_obligatoires(): void
    {
        $this->actingAs($this->gerant)
            ->post("/clients/{$this->cabinet->id}/adresses", ['libelle' => 'Sans adresse'])
            ->assertSessionHasErrors(['adresse_ligne_1', 'code_postal', 'ville']);
    }

    public function test_designer_une_nouvelle_adresse_principale_retire_le_drapeau_aux_autres(): void
    {
        $ancienne = AdresseLivraison::factory()->create([
            'laboratoire_id' => $this->laboratoire->id,
            'cabinet_id' => $this->cabinet->id,
            'est_principale' => true,
        ]);

        $this->actingAs($this->gerant)
            ->post("/clients/{$this->cabinet->id}/adresses", $this->adresseValide([
                'libelle' => 'Nouvelle antenne',
                'est_principale' => true,
            ]))
            ->assertRedirect();

        $this->assertFalse($ancienne->fresh()->est_principale);

        $principales = AdresseLivraison::withoutGlobalScope('laboratoire')
            ->where('cabinet_id', $this->cabinet->id)
            ->where('est_principale', true)
            ->pluck('libelle');

        $this->assertSame(['Nouvelle antenne'], $principales->all());
    }

    public function test_le_drapeau_principal_d_un_autre_cabinet_n_est_pas_touche(): void
    {
        $autreCabinet = Cabinet::factory()->create(['laboratoire_id' => $this->laboratoire->id]);
        $adresseDeLAutre = AdresseLivraison::factory()->create([
            'laboratoire_id' => $this->laboratoire->id,
            'cabinet_id' => $autreCabinet->id,
            'est_principale' => true,
        ]);

        $this->actingAs($this->gerant)
            ->post("/clients/{$this->cabinet->id}/adresses", $this->adresseValide(['est_principale' => true]))
            ->assertRedirect();

        $this->assertTrue($adresseDeLAutre->fresh()->est_principale);
    }

    public function test_une_adresse_se_modifie_puis_s_archive(): void
    {
        $adresse = AdresseLivraison::factory()->create([
            'laboratoire_id' => $this->laboratoire->id,
            'cabinet_id' => $this->cabinet->id,
        ]);

        $this->actingAs($this->gerant)
            ->put(
                "/clients/{$this->cabinet->id}/adresses/{$adresse->id}",
                $this->adresseValide(['libelle' => 'Antenne de Villeurbanne'])
            )
            ->assertRedirect();

        $this->assertSame('Antenne de Villeurbanne', $adresse->fresh()->libelle);

        $this->actingAs($this->gerant)
            ->post("/clients/{$this->cabinet->id}/adresses/{$adresse->id}/archiver")
            ->assertRedirect();

        $this->assertNotNull($adresse->fresh()->archive_le);
        $this->assertDatabaseHas('adresses_livraison', ['id' => $adresse->id]);
    }

    public function test_une_adresse_d_un_autre_laboratoire_est_introuvable(): void
    {
        $adresseDuConcurrent = AdresseLivraison::factory()->create(['libelle' => 'Intacte']);

        $this->actingAs($this->gerant)
            ->put(
                "/clients/{$this->cabinet->id}/adresses/{$adresseDuConcurrent->id}",
                $this->adresseValide(['libelle' => 'Tentative'])
            )
            ->assertNotFound();

        $this->assertSame('Intacte', $adresseDuConcurrent->fresh()->libelle);
    }
}
