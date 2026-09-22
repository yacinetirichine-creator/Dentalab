<?php

namespace Tests\Fixtures;

use App\Models\Concerns\AppartientAuLaboratoire;
use Illuminate\Database\Eloquent\Model;

/**
 * Modèle métier factice, qui n'existe que pour les tests.
 *
 * Il représente n'importe quelle table métier à venir (travaux, cabinets,
 * factures…) : toutes utiliseront le même trait, donc tester le trait ici
 * revient à tester le cloisonnement de toutes.
 */
class DonneeMetier extends Model
{
    use AppartientAuLaboratoire;

    protected $table = 'donnees_metier';

    protected $guarded = [];
}
