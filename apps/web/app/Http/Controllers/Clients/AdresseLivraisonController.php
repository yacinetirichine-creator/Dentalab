<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdresseLivraisonRequest;
use App\Models\AdresseLivraison;
use App\Models\Cabinet;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class AdresseLivraisonController extends Controller
{
    public function store(AdresseLivraisonRequest $requete, Cabinet $cabinet): RedirectResponse
    {
        DB::transaction(function () use ($requete, $cabinet) {
            $adresse = $cabinet->adressesLivraison()->create($requete->validated());

            $this->assurerUneSeulePrincipale($cabinet, $adresse);
        });

        return back()->with('status', __('interface.clients.messages.adresseAjoutee'));
    }

    public function update(AdresseLivraisonRequest $requete, Cabinet $cabinet, AdresseLivraison $adresse): RedirectResponse
    {
        $this->verifierAppartenance($cabinet, $adresse);

        DB::transaction(function () use ($requete, $cabinet, $adresse) {
            $adresse->update($requete->validated());

            $this->assurerUneSeulePrincipale($cabinet, $adresse);
        });

        return back()->with('status', __('interface.clients.messages.adresseModifiee'));
    }

    public function archive(Cabinet $cabinet, AdresseLivraison $adresse): RedirectResponse
    {
        $this->verifierAppartenance($cabinet, $adresse);

        $adresse->archiver();

        return back()->with('status', __('interface.clients.messages.adresseArchivee'));
    }

    public function restaure(Cabinet $cabinet, AdresseLivraison $adresse): RedirectResponse
    {
        $this->verifierAppartenance($cabinet, $adresse);

        $adresse->restaurer();

        return back()->with('status', __('interface.clients.messages.adresseRestauree'));
    }

    /**
     * Une seule adresse principale par cabinet : désigner une nouvelle
     * principale retire le drapeau aux autres.
     */
    protected function assurerUneSeulePrincipale(Cabinet $cabinet, AdresseLivraison $adresse): void
    {
        if (! $adresse->est_principale) {
            return;
        }

        $cabinet->adressesLivraison()
            ->whereKeyNot($adresse->getKey())
            ->update(['est_principale' => false]);
    }

    protected function verifierAppartenance(Cabinet $cabinet, AdresseLivraison $adresse): void
    {
        abort_unless($adresse->cabinet_id === $cabinet->id, 404);
    }
}
