<?php

namespace App\Enums;

/**
 * Les six profils décrits au §2 du cahier des charges.
 *
 * Les cinq premiers appartiennent à un laboratoire. L'administrateur de la
 * plateforme n'appartient à aucun : il gère les abonnements et le support, et
 * n'a par conséquent accès à aucune donnée métier d'aucun laboratoire.
 */
enum Role: string
{
    case Gerant = 'gerant';
    case Prothesiste = 'prothesiste';
    case Secretaire = 'secretaire';
    case Livreur = 'livreur';
    case Dentiste = 'dentiste';
    case AdministrateurPlateforme = 'administrateur_plateforme';

    public function libelle(): string
    {
        return __('roles.'.$this->value);
    }

    /**
     * @return list<Permission>
     */
    public function permissions(): array
    {
        return match ($this) {
            self::Gerant => [
                Permission::ParametrerLaboratoire,
                Permission::GererClients,
                Permission::GererCatalogue,
                Permission::Facturer,
                Permission::VoirIndicateurs,
                Permission::SaisirCommande,
                Permission::PreparerLivraison,
                Permission::ValiderEtapeFabrication,
            ],
            self::Prothesiste => [
                Permission::ValiderEtapeFabrication,
            ],
            self::Secretaire => [
                Permission::SaisirCommande,
                Permission::PreparerLivraison,
            ],
            self::Livreur => [
                Permission::VoirTournee,
            ],
            self::Dentiste => [
                Permission::CommanderEnLigne,
                Permission::SuivreSesTravaux,
            ],
            self::AdministrateurPlateforme => [
                Permission::AdministrerPlateforme,
            ],
        };
    }

    /**
     * La double authentification est-elle obligatoire pour ce rôle ?
     *
     * Exigée du gérant (§3 module 1 du cahier des charges), qui accède à la
     * facturation et aux paramètres du laboratoire, et de l'administrateur de
     * la plateforme, qui accède aux abonnements de tous les laboratoires.
     */
    public function doubleAuthentificationObligatoire(): bool
    {
        return match ($this) {
            self::Gerant, self::AdministrateurPlateforme => true,
            default => false,
        };
    }

    /** Ce rôle appartient-il à un laboratoire ? */
    public function appartientAUnLaboratoire(): bool
    {
        return $this !== self::AdministrateurPlateforme;
    }
}
