<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Force les rôles sensibles à activer la double authentification.
 *
 * Le gérant accède à la facturation et aux paramètres du laboratoire ;
 * l'administrateur de la plateforme accède aux abonnements de tous les
 * laboratoires. Pour eux, la double authentification n'est pas une option :
 * tant qu'elle n'est pas activée, ils sont renvoyés vers l'écran qui permet
 * de le faire.
 */
class ExigerDoubleAuthentification
{
    public function handle(Request $requete, Closure $suivant): Response
    {
        $utilisateur = $requete->user();

        if ($utilisateur?->doitActiverLaDoubleAuthentification()) {
            return redirect()
                ->route('double-authentification')
                ->with('status', 'Votre rôle exige la double authentification. Activez-la pour continuer.');
        }

        return $suivant($requete);
    }
}
