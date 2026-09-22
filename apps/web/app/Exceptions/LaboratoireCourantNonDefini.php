<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Levée quand une donnée métier est lue ou écrite sans qu'un laboratoire courant
 * soit défini.
 *
 * C'est volontairement une erreur, pas un silence : une requête non cloisonnée
 * exposerait les données d'un laboratoire à un autre. Si l'accès global est
 * légitime (console, administration de la plateforme), il doit être explicite,
 * via LaboratoireCourant::sansCloisonnement().
 */
class LaboratoireCourantNonDefini extends RuntimeException
{
    public static function pour(string $modele): self
    {
        return new self(
            "Aucun laboratoire courant n'est défini pour [{$modele}]. "
            .'Définissez-le avec LaboratoireCourant::definir(), ou enveloppez '
            ."l'accès dans LaboratoireCourant::sansCloisonnement() si l'accès "
            .'global est volontaire.'
        );
    }
}
