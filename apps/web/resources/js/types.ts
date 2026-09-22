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

/** Les propriétés que le serveur partage avec toutes les pages. */
export interface ProprietesPartagees {
    utilisateur: Utilisateur | null;
    menu: EntreeMenu[];
    message: string | null;
    [cle: string]: unknown;
}
