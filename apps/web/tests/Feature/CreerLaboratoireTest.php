<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Laboratoire;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreerLaboratoireTest extends TestCase
{
    use RefreshDatabase;

    public function test_la_commande_cree_le_laboratoire_et_son_gerant(): void
    {
        $this->artisan('laboratoire:creer', [
            '--nom' => 'Laboratoire Dupont',
            '--gerant' => 'Claire Dupont',
            '--email' => 'claire@dupont.test',
        ])->assertSuccessful();

        $laboratoire = Laboratoire::where('nom', 'Laboratoire Dupont')->sole();
        $gerant = User::where('email', 'claire@dupont.test')->sole();

        $this->assertSame($laboratoire->id, $gerant->laboratoire_id);
        $this->assertSame(Role::Gerant, $gerant->role);
    }

    public function test_la_commande_refuse_une_adresse_deja_utilisee(): void
    {
        User::factory()->create(['email' => 'deja@pris.test']);

        $this->artisan('laboratoire:creer', [
            '--nom' => 'Autre laboratoire',
            '--gerant' => 'Quelqu’un',
            '--email' => 'deja@pris.test',
        ])->assertFailed();

        $this->assertDatabaseMissing('laboratoires', ['nom' => 'Autre laboratoire']);
    }
}
