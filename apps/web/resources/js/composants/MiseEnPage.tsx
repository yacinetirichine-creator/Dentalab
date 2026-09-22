import { Link, router, usePage } from '@inertiajs/react';
import type { ReactNode } from 'react';
import { useTraduction } from '@/traductions';
import type { ProprietesPartagees } from '@/types';

interface Props {
    titre: string;
    children: ReactNode;
}

/**
 * Coquille commune aux écrans d'un utilisateur connecté : le menu, l'identité
 * et le laboratoire courant.
 *
 * Le menu affiché vient du serveur et ne contient que les entrées auxquelles
 * l'utilisateur a droit — mais chaque route se protège aussi elle-même.
 */
export default function MiseEnPage({ titre, children }: Props) {
    const { utilisateur, menu, message } = usePage<ProprietesPartagees>().props;
    const t = useTraduction();

    return (
        <div className="min-h-screen bg-neutral-50 text-neutral-900 dark:bg-neutral-950 dark:text-neutral-100">
            <header className="border-b border-neutral-200 dark:border-neutral-800">
                <div className="mx-auto flex max-w-5xl flex-wrap items-center justify-between gap-4 px-6 py-4">
                    <div>
                        <p className="text-sm font-semibold">Dentalab</p>
                        {utilisateur?.laboratoire && (
                            <p className="text-xs opacity-60">{utilisateur.laboratoire}</p>
                        )}
                    </div>

                    {utilisateur && (
                        <div className="flex items-center gap-4 text-sm">
                            <span className="opacity-70">
                                {utilisateur.nom} — {utilisateur.roleLibelle ?? t('commun.roleNonAttribue')}
                            </span>
                            <button
                                type="button"
                                onClick={() => router.post('/logout')}
                                className="rounded border border-neutral-300 px-3 py-1 hover:bg-neutral-100 dark:border-neutral-700 dark:hover:bg-neutral-800"
                            >
                                {t('commun.seDeconnecter')}
                            </button>
                        </div>
                    )}
                </div>

                {menu.length > 0 && (
                    <nav className="mx-auto max-w-5xl px-6 pb-3">
                        <ul className="flex flex-wrap gap-4 text-sm">
                            {menu.map((entree) => (
                                <li key={entree.route}>
                                    <Link href={entree.url} className="hover:underline">
                                        {entree.libelle}
                                    </Link>
                                </li>
                            ))}
                        </ul>
                    </nav>
                )}
            </header>

            <main className="mx-auto max-w-5xl px-6 py-10">
                {message && (
                    <p className="mb-6 rounded border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-900 dark:border-amber-700 dark:bg-amber-950 dark:text-amber-100">
                        {message}
                    </p>
                )}

                <h1 className="mb-6 text-2xl font-semibold">{titre}</h1>

                {children}
            </main>
        </div>
    );
}
