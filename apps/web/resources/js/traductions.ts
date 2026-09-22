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
        valeur,
    );
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
