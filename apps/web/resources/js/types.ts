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
