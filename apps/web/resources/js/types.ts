export interface Utilisateur {
    nom: string;
    email: string;
    role: string | null;
    roleLibelle: string | null;
    laboratoire: string | null;
    doubleAuthentificationActivee: boolean;
    permissions: string[];
}

export interface EntreeMenu {
    libelle: string;
    route: string;
    url: string;
}

/** Un arbre de traductions : des chaînes, éventuellement imbriquées. */
export interface ArbreDeTraductions {
    [cle: string]: string | ArbreDeTraductions;
}

/** Les propriétés que le serveur partage avec toutes les pages. */
export interface ProprietesPartagees {
    utilisateur: Utilisateur | null;
    menu: EntreeMenu[];
    message: string | null;
    traductions: ArbreDeTraductions;
    [cle: string]: unknown;
}

export interface Cabinet {
    id: number;
    raison_sociale: string;
    siret: string | null;
    numero_tva: string | null;
    email: string | null;
    telephone: string | null;
    adresse_ligne_1: string | null;
    adresse_ligne_2: string | null;
    code_postal: string | null;
    ville: string | null;
    pays: string;
    delai_reglement_jours: number;
    notes: string | null;
    archive_le: string | null;
    praticiens_actifs_count?: number;
    praticiens?: Praticien[];
    adresses_livraison?: AdresseLivraison[];
}

export interface Praticien {
    id: number;
    cabinet_id: number;
    civilite: string | null;
    nom: string;
    prenom: string | null;
    numero_rpps: string | null;
    email: string | null;
    telephone: string | null;
    archive_le: string | null;
}

export interface AdresseLivraison {
    id: number;
    cabinet_id: number;
    libelle: string;
    adresse_ligne_1: string;
    adresse_ligne_2: string | null;
    code_postal: string;
    ville: string;
    pays: string;
    contact: string | null;
    telephone: string | null;
    instructions: string | null;
    est_principale: boolean;
    archive_le: string | null;
}
