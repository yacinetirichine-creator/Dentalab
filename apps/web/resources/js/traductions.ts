import { usePage } from '@inertiajs/react';
import type { ArbreDeTraductions, ProprietesPartagees } from '@/types';

type Remplacements = Record<string, string | number>;

/**
 * Résout une clé pointée (« connexion.email ») dans l'arbre de traductions,
 * et remplace les marqueurs :nom par les valeurs fournies.
 *
 * Une clé absente est renvoyée telle quelle : mieux vaut voir
 * « connexion.email » à l'écran qu'un blanc silencieux.
 */
export function traduire(
    arbre: ArbreDeTraductions,
    cle: string,
    remplacements: Remplacements = {},
): string {
    const valeur = cle.split('.').reduce<string | ArbreDeTraductions | undefined>(
        (courant, morceau) =>
            typeof courant === 'object' && courant !== null ? courant[morceau] : undefined,
        arbre,
    );

    if (typeof valeur !== 'string') {
        return cle;
    }

    return Object.entries(remplacements).reduce(
        (texte, [nom, remplacement]) => texte.replaceAll(`:${nom}`, String(remplacement)),
        accorder(valeur, remplacements.nombre),
    );
}

/**
 * Choisit la forme d'une chaîne au pluriel : « :nombre praticien|:nombre praticiens ».
 *
 * Règle du français : zéro et un restent au singulier, le reste au pluriel.
 * Les langues à plus de deux formes demanderont mieux ; ce sera le moment de
 * poser une vraie bibliothèque plutôt que d'étoffer celle-ci.
 */
function accorder(valeur: string, nombre: string | number | undefined): string {
    if (!valeur.includes('|')) {
        return valeur;
    }

    const formes = valeur.split('|');
    const compte = typeof nombre === 'number' ? nombre : Number(nombre);

    if (Number.isNaN(compte)) {
        return formes[0];
    }

    return compte <= 1 ? formes[0] : formes[formes.length - 1];
}

/**
 * Le crochet à utiliser dans les composants :
 *
 *     const t = useTraduction();
 *     <button>{t('connexion.seConnecter')}</button>
 */
export function useTraduction() {
    const { traductions } = usePage<ProprietesPartagees>().props;

    return (cle: string, remplacements?: Remplacements) =>
        traduire(traductions, cle, remplacements);
}
