<?php

namespace App\Models;

use App\Models\Concerns\AppartientAuLaboratoire;
use App\Models\Concerns\Archivable;
use Database\Factories\PraticienFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Un dentiste, rattaché à un cabinet.
 *
 * C'est lui qui commande. Le cabinet reste le client facturé.
 */
#[Fillable(['civilite', 'nom', 'prenom', 'numero_rpps', 'email', 'telephone'])]
class Praticien extends Model
{
    use AppartientAuLaboratoire;
    use Archivable;

    /** @use HasFactory<PraticienFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return ['archive_le' => 'datetime'];
    }

    /** @return BelongsTo<Cabinet, $this> */
    public function cabinet(): BelongsTo
    {
        return $this->belongsTo(Cabinet::class);
    }

    public function nomComplet(): string
    {
        return trim(implode(' ', array_filter([$this->civilite, $this->prenom, $this->nom])));
    }
}
