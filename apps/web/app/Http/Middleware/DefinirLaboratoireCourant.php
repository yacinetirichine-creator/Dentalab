<?php

namespace App\Http\Middleware;

use App\Support\Tenancy\LaboratoireCourant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Renseigne le laboratoire courant à partir de l'utilisateur connecté.
 *
 * Le laboratoire ne vient JAMAIS de la requête (paramètre d'URL, en-tête,
 * champ de formulaire) : il vient toujours du compte connecté. Sinon un
 * utilisateur pourrait désigner le laboratoire d'un concurrent.
 */
class DefinirLaboratoireCourant
{
    public function __construct(protected LaboratoireCourant $laboratoireCourant) {}

    public function handle(Request $requete, Closure $suivant): Response
    {
        $utilisateur = $requete->user();

        if ($utilisateur?->laboratoire_id !== null) {
            $this->laboratoireCourant->definir($utilisateur->laboratoire_id);
        }

        return $suivant($requete);
    }
}
