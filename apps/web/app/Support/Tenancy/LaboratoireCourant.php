<?php

namespace App\Support\Tenancy;

use App\Models\Laboratoire;
use Illuminate\Support\Facades\Auth;

/**
 * Le laboratoire pour lequel la requête en cours travaille.
 *
 * Il vient du compte connecté, et de nulle part ailleurs : jamais d'un
 * paramètre d'URL, d'un en-tête ou d'un champ de formulaire. Sinon un
 * utilisateur pourrait désigner le laboratoire d'un concurrent.
 *
 * La résolution est paresseuse, faite au premier besoin, et non posée par un
 * middleware : le framework résout les paramètres d'URL (`/clients/{cabinet}`)
 * à l'intérieur de sa propre pile de middlewares, donc avant tout middleware
 * applicatif ajouté à la suite. Une lecture cloisonnée peut ainsi arriver
 * avant lui. Dépendre de l'ordre des middlewares serait fragile ; dépendre de
 * l'utilisateur connecté ne l'est pas.
 */
class LaboratoireCourant
{
    protected ?int $id = null;

    /** Vrai pendant un appel à sansCloisonnement(). */
    protected bool $cloisonnementDesactive = false;

    /**
     * Force un laboratoire, indépendamment du compte connecté.
     *
     * Utile en console, dans les tests, et partout où il n'y a pas de
     * requête HTTP.
     */
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
        return $this->id ??= Auth::user()?->laboratoire_id;
    }

    public function estDefini(): bool
    {
        return $this->id() !== null;
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
