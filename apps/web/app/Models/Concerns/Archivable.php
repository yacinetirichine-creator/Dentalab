<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Archiver plutôt que supprimer.
 *
 * Un cabinet, un praticien ou une adresse portent un historique de travaux,
 * de factures et de fiches de traçabilité. Rien de tout cela ne doit
 * disparaître parce qu'un client a cessé son activité : on archive, ce qui
 * retire l'élément des listes sans toucher à son passé.
 *
 * Volontairement pas un « global scope » : l'archivage est un état métier,
 * pas une suppression déguisée. Les requêtes disent explicitement ce
 * qu'elles veulent voir.
 *
 * @phpstan-require-extends Model
 */
trait Archivable
{
    public function archiver(): void
    {
        $this->forceFill(['archive_le' => now()])->save();
    }

    public function restaurer(): void
    {
        $this->forceFill(['archive_le' => null])->save();
    }

    public function estArchive(): bool
    {
        return $this->archive_le !== null;
    }

    /** @param  Builder<static>  $requete */
    public function scopeActifs(Builder $requete): void
    {
        $requete->whereNull('archive_le');
    }

    /** @param  Builder<static>  $requete */
    public function scopeArchives(Builder $requete): void
    {
        $requete->whereNotNull('archive_le');
    }
}
