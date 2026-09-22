<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Http\Requests\PraticienRequest;
use App\Models\Cabinet;
use App\Models\Praticien;
use Illuminate\Http\RedirectResponse;

class PraticienController extends Controller
{
    public function store(PraticienRequest $requete, Cabinet $cabinet): RedirectResponse
    {
        $cabinet->praticiens()->create($requete->validated());

        return back()->with('status', __('interface.clients.messages.praticienAjoute'));
    }

    public function update(PraticienRequest $requete, Cabinet $cabinet, Praticien $praticien): RedirectResponse
    {
        $this->verifierAppartenance($cabinet, $praticien);

        $praticien->update($requete->validated());

        return back()->with('status', __('interface.clients.messages.praticienModifie'));
    }

    public function archive(Cabinet $cabinet, Praticien $praticien): RedirectResponse
    {
        $this->verifierAppartenance($cabinet, $praticien);

        $praticien->archiver();

        return back()->with('status', __('interface.clients.messages.praticienArchive'));
    }

    public function restaure(Cabinet $cabinet, Praticien $praticien): RedirectResponse
    {
        $this->verifierAppartenance($cabinet, $praticien);

        $praticien->restaurer();

        return back()->with('status', __('interface.clients.messages.praticienRestaure'));
    }

    /**
     * Le cloisonnement entre laboratoires est déjà assuré par le modèle.
     * Ici on vérifie l'imbrication : un praticien d'un autre cabinet du même
     * laboratoire ne se modifie pas depuis cette fiche.
     */
    protected function verifierAppartenance(Cabinet $cabinet, Praticien $praticien): void
    {
        abort_unless($praticien->cabinet_id === $cabinet->id, 404);
    }
}
