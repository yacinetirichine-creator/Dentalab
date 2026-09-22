<?php

namespace App\Models;

use Database\Factories\LaboratoireFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Un laboratoire de prothèses dentaires : le client abonné à la plateforme.
 *
 * C'est la racine du cloisonnement. Toute donnée métier appartient à un
 * laboratoire et ne doit jamais être visible depuis un autre.
 */
#[Fillable(['nom', 'siret', 'ville', 'prefixe_numerotation', 'actif'])]
class Laboratoire extends Model
{
    /** @use HasFactory<LaboratoireFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'actif' => 'boolean',
        ];
    }

    /** @return HasMany<User, $this> */
    public function utilisateurs(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
