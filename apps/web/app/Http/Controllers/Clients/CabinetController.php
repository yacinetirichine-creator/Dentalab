<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Http\Requests\CabinetRequest;
use App\Models\Cabinet;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CabinetController extends Controller
{
    public function index(Request $requete): Response
    {
        $archives = $requete->boolean('archives');

        $cabinets = Cabinet::query()
            ->when($archives, fn ($q) => $q->archives(), fn ($q) => $q->actifs())
            ->withCount([
                'praticiens as praticiens_actifs_count' => fn ($q) => $q->actifs(),
            ])
            ->orderBy('raison_sociale')
            ->get();

        return Inertia::render('clients/Liste', [
            'cabinets' => $cabinets,
            'archives' => $archives,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('clients/Formulaire', ['cabinet' => null]);
    }

    public function store(CabinetRequest $requete): RedirectResponse
    {
        $cabinet = Cabinet::create($requete->validated());

        return redirect()
            ->route('clients.show', $cabinet)
            ->with('status', __('interface.clients.messages.cabinetCree'));
    }

    public function show(Cabinet $cabinet): Response
    {
        $cabinet->load([
            'praticiens' => fn ($q) => $q->orderBy('nom'),
            'adressesLivraison' => fn ($q) => $q->orderByDesc('est_principale')->orderBy('libelle'),
        ]);

        return Inertia::render('clients/Fiche', ['cabinet' => $cabinet]);
    }

    public function edit(Cabinet $cabinet): Response
    {
        return Inertia::render('clients/Formulaire', ['cabinet' => $cabinet]);
    }

    public function update(CabinetRequest $requete, Cabinet $cabinet): RedirectResponse
    {
        $cabinet->update($requete->validated());

        return redirect()
            ->route('clients.show', $cabinet)
            ->with('status', __('interface.clients.messages.cabinetModifie'));
    }

    /**
     * Archive le cabinet — jamais de suppression : son historique de travaux
     * et de factures doit être conservé.
     */
    public function archive(Cabinet $cabinet): RedirectResponse
    {
        $cabinet->archiver();

        return redirect()
            ->route('clients.index')
            ->with('status', __('interface.clients.messages.cabinetArchive'));
    }

    public function restaure(Cabinet $cabinet): RedirectResponse
    {
        $cabinet->restaurer();

        return redirect()
            ->route('clients.show', $cabinet)
            ->with('status', __('interface.clients.messages.cabinetRestaure'));
    }
}
