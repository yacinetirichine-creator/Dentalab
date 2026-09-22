<?php

namespace App\Enums;

/**
 * Ce qu'un utilisateur a le droit de faire.
 *
 * Les permissions sont attachées aux rôles (voir Role::permissions()), jamais
 * aux personnes : un laboratoire ne peut pas donner à son livreur le droit de
 * facturer. Si un besoin de permission individuelle apparaît sur le terrain,
 * ce sera un lot à part entière, pas une rustine.
 */
enum Permission: string
{
    case ParametrerLaboratoire = 'parametrer-laboratoire';
    case GererClients = 'gerer-clients';
    case GererCatalogue = 'gerer-catalogue';
    case Facturer = 'facturer';
    case VoirIndicateurs = 'voir-indicateurs';
    case SaisirCommande = 'saisir-commande';
    case PreparerLivraison = 'preparer-livraison';
    case ValiderEtapeFabrication = 'valider-etape-fabrication';
    case VoirTournee = 'voir-tournee';
    case CommanderEnLigne = 'commander-en-ligne';
    case SuivreSesTravaux = 'suivre-ses-travaux';
    case AdministrerPlateforme = 'administrer-plateforme';

    public function libelle(): string
    {
        return __('permissions.'.$this->value);
    }
}
