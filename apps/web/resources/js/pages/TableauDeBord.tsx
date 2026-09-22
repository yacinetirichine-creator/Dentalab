import { Head, Link, usePage } from '@inertiajs/react';
import MiseEnPage from '@/composants/MiseEnPage';
import { useTraduction } from '@/traductions';
import type { ProprietesPartagees } from '@/types';

export default function TableauDeBord() {
    const { utilisateur, menu } = usePage<ProprietesPartagees>().props;
    const t = useTraduction();

    return (
        <MiseEnPage titre={t('tableauDeBord.titre')}>
            <Head title={t('tableauDeBord.titre')} />

            <div className="space-y-8 text-sm leading-relaxed">
                <p>
                    {t('tableauDeBord.salutation', { nom: utilisateur?.nom ?? '' })}{' '}
                    <strong>{utilisateur?.roleLibelle ?? t('tableauDeBord.sansRole')}</strong>
                    {utilisateur?.laboratoire && (
                        <>
                            {' '}
                            {t('tableauDeBord.auLaboratoire', { laboratoire: utilisateur.laboratoire })}
                        </>
                    )}
                    .
                </p>

                {menu.length <= 1 ? (
                    <p className="rounded border border-neutral-300 px-4 py-3 opacity-70 dark:border-neutral-700">
                        {t('tableauDeBord.aucunEcran')}
                    </p>
                ) : (
                    <div>
                        <p className="mb-3 font-medium">{t('tableauDeBord.cePourquoiVousEtesHabilite')}</p>
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
                            {t('doubleAuthentification.invitation')}
                        </Link>{' '}
                        {t('doubleAuthentification.invitationSuite')}
                    </p>
                )}
            </div>
        </MiseEnPage>
    );
}
