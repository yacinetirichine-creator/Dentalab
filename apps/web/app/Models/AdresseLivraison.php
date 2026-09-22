<?php

namespace App\Models;

use App\Models\Concerns\AppartientAuLaboratoire;
use App\Models\Concerns\Archivable;
use Database\Factories\AdresseLivraisonFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Un endroit où porter les travaux d'un cabinet.
 *
 * Séparée de l'adresse de facturation : un cabinet peut avoir plusieurs
 * sites, et le travail ne part pas forcément là où va la facture.
 */
#[Fillable([
    'libelle', 'adresse_ligne_1', 'adresse_ligne_2', 'code_postal', 'ville',
    'pays', 'contact', 'telephone', 'instructions', 'est_principale',
])]
class AdresseLivraison extends Model
{
    use AppartientAuLaboratoire;
    use Archivable;

    /** @use HasFactory<AdresseLivraisonFactory> */
    use HasFactory;

    protected $table = 'adresses_livraison';

    protected function casts(): array
    {
        return [
            'archive_le' => 'datetime',
            'est_principale' => 'boolean',
        ];
    }

    /** @return BelongsTo<Cabinet, $this> */
    public function cabinet(): BelongsTo
    {
        return $this->belongsTo(Cabinet::class);
    }
}
