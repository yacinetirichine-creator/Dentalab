<?php

namespace App\Support\Tenancy;

use App\Models\Laboratoire;

/**
 * Retient le laboratoire pour lequel la requête en cours travaille.
 *
 * Enregistré en singleton : le middleware le renseigne à partir de
 * l'utilisateur connecté, et le trait AppartientAuLaboratoire s'en sert pour
 * filtrer automatiquement toutes les requêtes métier.
 */
class LaboratoireCourant
{
    protected ?int $id = null;

    /** Vrai pendant un appel à sansCloisonnement(). */
    protected bool $cloisonnementDesactive = false;

    public function definir(Laboratoire|int|null $laboratoire): void
    {
        $this->id = $laboratoire instanceof Laboratoire
            ? $laboratoire->getKey()
            : $laboratoire;
    }

    public function oublier(): void
    {
        $this->id = null;
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function estDefini(): bool
    {
        return $this->id !== null;
    }

    public function cloisonnementActif(): bool
    {
        return ! $this->cloisonnementDesactive;
    }

    /**
     * Exécute un traitement sans filtrage par laboratoire.
     *
     * Réservé aux cas où l'accès global est légitime et assumé : commandes
     * artisan, administration de la plateforme, migrations de données. Jamais
     * dans le code servant une requête d'un utilisateur de laboratoire.
     *
     * @template T
     *
     * @param  callable(): T  $traitement
     * @return T
     */
    public function sansCloisonnement(callable $traitement): mixed
    {
        $precedent = $this->cloisonnementDesactive;
        $this->cloisonnementDesactive = true;

        try {
            return $traitement();
        } finally {
            $this->cloisonnementDesactive = $precedent;
        }
    }
}
