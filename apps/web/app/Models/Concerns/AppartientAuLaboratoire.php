<?php

namespace App\Models\Concerns;

use App\Exceptions\LaboratoireCourantNonDefini;
use App\Models\Laboratoire;
use App\Support\Tenancy\LaboratoireCourant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * À appliquer sur TOUTE table métier (règle non négociable n° 1 du projet).
 *
 * Le trait fait deux choses :
 *  - il filtre automatiquement chaque requête sur le laboratoire courant ;
 *  - il renseigne laboratoire_id à la création, pour qu'on ne puisse pas
 *    l'oublier.
 *
 * Sans laboratoire courant défini, il lève une exception plutôt que de
 * retourner des données de tous les laboratoires.
 *
 * @phpstan-require-extends Model
 */
trait AppartientAuLaboratoire
{
    public static function bootAppartientAuLaboratoire(): void
    {
        static::addGlobalScope('laboratoire', function (Builder $requete) {
            $courant = app(LaboratoireCourant::class);

            if (! $courant->cloisonnementActif()) {
                return;
            }

            if (! $courant->estDefini()) {
                throw LaboratoireCourantNonDefini::pour(static::class);
            }

            $requete->where(
                $requete->getModel()->qualifyColumn('laboratoire_id'),
                $courant->id()
            );
        });

        static::creating(function (Model $modele) {
            if ($modele->getAttribute('laboratoire_id') !== null) {
                return;
            }

            $courant = app(LaboratoireCourant::class);

            if (! $courant->estDefini()) {
                throw LaboratoireCourantNonDefini::pour(static::class);
            }

            $modele->setAttribute('laboratoire_id', $courant->id());
        });
    }

    /** @return BelongsTo<Laboratoire, $this> */
    public function laboratoire(): BelongsTo
    {
        return $this->belongsTo(Laboratoire::class);
    }
}
