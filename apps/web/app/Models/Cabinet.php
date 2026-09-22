<?php

namespace App\Models;

use App\Models\Concerns\AppartientAuLaboratoire;
use App\Models\Concerns\Archivable;
use Database\Factories\CabinetFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Un cabinet dentaire client du laboratoire.
 *
 * C'est le client au sens commercial : c'est lui qu'on facture. Les
 * praticiens qui lui sont rattachés passent les commandes, et les adresses
 * de livraison disent où porter les travaux.
 */
#[Fillable([
    'raison_sociale', 'siret', 'numero_tva', 'email', 'telephone',
    'adresse_ligne_1', 'adresse_ligne_2', 'code_postal', 'ville', 'pays',
    'delai_reglement_jours', 'notes',
])]
class Cabinet extends Model
{
    use AppartientAuLaboratoire;
    use Archivable;

    /** @use HasFactory<CabinetFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'archive_le' => 'datetime',
            'delai_reglement_jours' => 'integer',
        ];
    }

    /** @return HasMany<Praticien, $this> */
    public function praticiens(): HasMany
    {
        return $this->hasMany(Praticien::class);
    }

    /** @return HasMany<AdresseLivraison, $this> */
    public function adressesLivraison(): HasMany
    {
        return $this->hasMany(AdresseLivraison::class);
    }
}
