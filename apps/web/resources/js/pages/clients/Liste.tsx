import { Head, Link } from '@inertiajs/react';
import MiseEnPage from '@/composants/MiseEnPage';
import { useTraduction } from '@/traductions';
import type { Cabinet } from '@/types';

interface Props {
    cabinets: Cabinet[];
    archives: boolean;
}

export default function Liste({ cabinets, archives }: Props) {
    const t = useTraduction();

    return (
        <MiseEnPage titre={t('clients.titre')}>
            <Head title={t('clients.titre')} />

            <div className="mb-6 flex flex-wrap items-center gap-4 text-sm">
                <Link
                    href="/clients/nouveau"
                    className="rounded bg-neutral-900 px-4 py-2 text-white dark:bg-neutral-100 dark:text-neutral-900"
                >
                    {t('clients.nouveau')}
                </Link>

                <Link href={archives ? '/clients' : '/clients?archives=1'} className="underline opacity-70">
                    {archives ? t('clients.voirLesActifs') : t('clients.voirLesArchives')}
                </Link>
            </div>

            {cabinets.length === 0 ? (
                <p className="rounded border border-neutral-300 px-4 py-3 text-sm opacity-70 dark:border-neutral-700">
                    {archives ? t('clients.aucunCabinetArchive') : t('clients.aucunCabinet')}
                </p>
            ) : (
                <ul className="divide-y divide-neutral-200 rounded border border-neutral-200 dark:divide-neutral-800 dark:border-neutral-800">
                    {cabinets.map((cabinet) => (
                        <li key={cabinet.id} className="flex flex-wrap items-baseline justify-between gap-2 px-4 py-3">
                            <Link href={`/clients/${cabinet.id}`} className="font-medium underline">
                                {cabinet.raison_sociale}
                            </Link>

                            <span className="text-sm opacity-60">
                                {[
                                    cabinet.ville,
                                    t('clients.nombreDePraticiens', {
                                        nombre: cabinet.praticiens_actifs_count ?? 0,
                                    }),
                                ]
                                    .filter(Boolean)
                                    .join(' · ')}
                            </span>
                        </li>
                    ))}
                </ul>
            )}
        </MiseEnPage>
    );
}
