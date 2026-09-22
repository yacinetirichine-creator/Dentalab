<?php

use App\Enums\Permission;
use App\Http\Middleware\ExigerDoubleAuthentification;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', fn () => Inertia::render('Accueil', [
    'version' => config('app.version'),
]))->name('accueil');

Route::middleware('auth')->group(function () {
    // Écran d'activation de la double authentification. Volontairement hors du
    // groupe ci-dessous : c'est la page vers laquelle on redirige justement
    // ceux à qui elle manque.
    Route::get('/double-authentification', function () {
        $utilisateur = request()->user();

        return Inertia::render('DoubleAuthentification', [
            'obligatoire' => (bool) $utilisateur->role?->doubleAuthentificationObligatoire(),
            'activee' => $utilisateur->two_factor_confirmed_at !== null,
            'enAttenteDeConfirmation' => $utilisateur->two_factor_secret !== null
                && $utilisateur->two_factor_confirmed_at === null,
        ]);
    })->name('double-authentification');

    Route::middleware(ExigerDoubleAuthentification::class)->group(function () {
        Route::get('/tableau-de-bord', fn () => Inertia::render('TableauDeBord'))
            ->name('tableau-de-bord');

        // Écrans à construire dans les lots suivants. Ils existent dès
        // maintenant pour que les droits de chaque rôle soient testés.
        // Le titre est traduit au rendu, pas à la déclaration de la route :
        // la langue dépend de la requête, pas du démarrage de l'application.
        $chantier = fn (string $cleDuTitre, string $lot) => fn () => Inertia::render('Chantier', [
            'titre' => __('interface.ecrans.'.$cleDuTitre),
            'lot' => $lot,
        ]);

        Route::get('/parametres', $chantier('parametres', '1.4'))
            ->middleware('can:'.Permission::ParametrerLaboratoire->value)
            ->name('parametres');

        Route::get('/facturation', $chantier('facturation', '3.4'))
            ->middleware('can:'.Permission::Facturer->value)
            ->name('facturation');

        Route::get('/commandes/nouvelle', $chantier('nouvelleCommande', '2.1'))
            ->middleware('can:'.Permission::SaisirCommande->value)
            ->name('commandes.nouvelle');

        Route::get('/atelier', $chantier('atelier', '2.5'))
            ->middleware('can:'.Permission::ValiderEtapeFabrication->value)
            ->name('atelier');

        Route::get('/tournee', $chantier('tournee', '5.5'))
            ->middleware('can:'.Permission::VoirTournee->value)
            ->name('tournee');

        Route::get('/mes-travaux', $chantier('mesTravaux', '5.1'))
            ->middleware('can:'.Permission::SuivreSesTravaux->value)
            ->name('mes-travaux');
    });
});
