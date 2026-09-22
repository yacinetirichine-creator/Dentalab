<?php

namespace Tests\Feature;

use App\Exceptions\LaboratoireCourantNonDefini;
use App\Models\Laboratoire;
use App\Support\Tenancy\LaboratoireCourant;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\Fixtures\DonneeMetier;
use Tests\TestCase;

/**
 * Règle non négociable n° 1 : un laboratoire ne voit jamais les données d'un
 * autre. Ces tests portent sur le trait AppartientAuLaboratoire, donc sur
 * toutes les tables métier qui l'utiliseront.
 */
class CloisonnementTest extends TestCase
{
    use RefreshDatabase;

    protected Laboratoire $laboA;

    protected Laboratoire $laboB;

    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('donnees_metier', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laboratoire_id')->constrained();
            $table->string('libelle');
            $table->timestamps();
        });

        $this->laboA = Laboratoire::factory()->create(['nom' => 'Labo A']);
        $this->laboB = Laboratoire::factory()->create(['nom' => 'Labo B']);
    }

    protected function courant(): LaboratoireCourant
    {
        return app(LaboratoireCourant::class);
    }

    public function test_un_laboratoire_ne_voit_que_ses_propres_donnees(): void
    {
        $this->courant()->definir($this->laboA);
        DonneeMetier::create(['libelle' => 'Travail du labo A']);

        $this->courant()->definir($this->laboB);
        DonneeMetier::create(['libelle' => 'Travail du labo B']);

        $this->courant()->definir($this->laboA);

        $this->assertSame(
            ['Travail du labo A'],
            DonneeMetier::pluck('libelle')->all()
        );
    }

    public function test_une_donnee_d_un_autre_laboratoire_est_introuvable_par_son_identifiant(): void
    {
        $this->courant()->definir($this->laboA);
        $donneeDuLaboA = DonneeMetier::create(['libelle' => 'Confidentiel']);

        $this->courant()->definir($this->laboB);

        $this->assertNull(DonneeMetier::find($donneeDuLaboA->id));
    }

    public function test_le_laboratoire_est_renseigne_automatiquement_a_la_creation(): void
    {
        $this->courant()->definir($this->laboA);

        $donnee = DonneeMetier::create(['libelle' => 'Sans laboratoire_id explicite']);

        $this->assertSame($this->laboA->id, $donnee->laboratoire_id);
    }

    public function test_une_lecture_sans_laboratoire_courant_est_refusee(): void
    {
        $this->courant()->oublier();

        $this->expectException(LaboratoireCourantNonDefini::class);

        DonneeMetier::all();
    }

    public function test_une_creation_sans_laboratoire_courant_est_refusee(): void
    {
        $this->courant()->oublier();

        $this->expectException(LaboratoireCourantNonDefini::class);

        DonneeMetier::create(['libelle' => 'Orpheline']);
    }

    public function test_l_acces_global_reste_possible_mais_doit_etre_explicite(): void
    {
        $this->courant()->definir($this->laboA);
        DonneeMetier::create(['libelle' => 'Travail du labo A']);

        $this->courant()->definir($this->laboB);
        DonneeMetier::create(['libelle' => 'Travail du labo B']);

        $this->courant()->oublier();

        $tout = $this->courant()->sansCloisonnement(
            fn () => DonneeMetier::pluck('libelle')->all()
        );

        $this->assertCount(2, $tout);
    }

    public function test_le_cloisonnement_est_retabli_apres_un_acces_global(): void
    {
        $this->courant()->definir($this->laboA);

        $this->courant()->sansCloisonnement(fn () => DonneeMetier::all());

        $this->assertTrue($this->courant()->cloisonnementActif());
    }
}
