import { Head, Link, usePage } from '@inertiajs/react';
import MiseEnPage from '@/composants/MiseEnPage';
import type { ProprietesPartagees } from '@/types';

export default function TableauDeBord() {
    const { utilisateur, menu } = usePage<ProprietesPartagees>().props;

    return (
        <MiseEnPage titre="Tableau de bord">
            <Head title="Tableau de bord" />

            <div className="space-y-8 text-sm leading-relaxed">
                <p>
                    Bonjour {utilisateur?.nom}. Vous êtes connecté en tant que{' '}
                    <strong>{utilisateur?.roleLibelle ?? 'utilisateur sans rôle attribué'}</strong>
                    {utilisateur?.laboratoire && <> au laboratoire {utilisateur.laboratoire}</>}.
                </p>

                {menu.length <= 1 ? (
                    <p className="rounded border border-neutral-300 px-4 py-3 opacity-70 dark:border-neutral-700">
                        Aucun écran ne vous est ouvert pour l’instant. Si c’est inattendu,
                        demandez au gérant de vérifier votre rôle.
                    </p>
                ) : (
                    <div>
                        <p className="mb-3 font-medium">Ce que vous pouvez faire</p>
                        <ul className="space-y-2">
                            {menu
                                .filter((entree) => entree.route !== 'tableau-de-bord')
                                .map((entree) => (
                                    <li key={entree.route}>
                                        <Link href={entree.url} className="underline">
                                            {entree.libelle}
                                        </Link>
                                    </li>
                                ))}
                        </ul>
                    </div>
                )}

                {utilisateur && !utilisateur.doubleAuthentificationActivee && (
                    <p className="text-xs opacity-60">
                        <Link href="/double-authentification" className="underline">
                            Activer la double authentification
                        </Link>{' '}
                        pour protéger votre compte.
                    </p>
                )}
            </div>
        </MiseEnPage>
    );
}
