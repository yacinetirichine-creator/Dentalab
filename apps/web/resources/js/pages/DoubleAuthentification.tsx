import { Head, router, useForm } from '@inertiajs/react';
import { type FormEvent, useEffect, useState } from 'react';
import MiseEnPage from '@/composants/MiseEnPage';
import { useTraduction } from '@/traductions';

interface Props {
    /** Le rôle de l'utilisateur impose-t-il la double authentification ? */
    obligatoire: boolean;
    /** Activée et confirmée. */
    activee: boolean;
    /** Activée mais pas encore confirmée par un premier code. */
    enAttenteDeConfirmation: boolean;
}

export default function DoubleAuthentification({ obligatoire, activee, enAttenteDeConfirmation }: Props) {
    const t = useTraduction();
    const [qr, setQr] = useState<string | null>(null);
    const [codesDeSecours, setCodesDeSecours] = useState<string[]>([]);
    const confirmation = useForm({ code: '' });

    useEffect(() => {
        if (!enAttenteDeConfirmation) {
            return;
        }

        fetch('/user/two-factor-qr-code', { headers: { Accept: 'application/json' } })
            .then((reponse) => (reponse.ok ? reponse.json() : null))
            .then((donnees) => setQr(donnees?.svg ?? null))
            .catch(() => setQr(null));
    }, [enAttenteDeConfirmation]);

    useEffect(() => {
        if (!activee) {
            return;
        }

        fetch('/user/two-factor-recovery-codes', { headers: { Accept: 'application/json' } })
            .then((reponse) => (reponse.ok ? reponse.json() : []))
            .then((codes) => setCodesDeSecours(Array.isArray(codes) ? codes : []))
            .catch(() => setCodesDeSecours([]));
    }, [activee]);

    function confirmer(evenement: FormEvent) {
        evenement.preventDefault();
        confirmation.post('/user/confirmed-two-factor-authentication');
    }

    return (
        <MiseEnPage titre={t('doubleAuthentification.titre')}>
            <Head title={t('doubleAuthentification.titre')} />

            <div className="max-w-xl space-y-6 text-sm leading-relaxed">
                {obligatoire && !activee && (
                    <p className="rounded border border-amber-300 bg-amber-50 px-4 py-3 text-amber-900 dark:border-amber-700 dark:bg-amber-950 dark:text-amber-100">
                        {t('doubleAuthentification.exigee')}
                    </p>
                )}

                {!activee && !enAttenteDeConfirmation && (
                    <>
                        <p>{t('doubleAuthentification.principe')}</p>
                        <button
                            type="button"
                            onClick={() => router.post('/user/two-factor-authentication')}
                            className="rounded bg-neutral-900 px-4 py-2 text-white dark:bg-neutral-100 dark:text-neutral-900"
                        >
                            {t('doubleAuthentification.activer')}
                        </button>
                    </>
                )}

                {enAttenteDeConfirmation && (
                    <>
                        <p>{t('doubleAuthentification.consigneScan')}</p>

                        {qr ? (
                            <div
                                className="inline-block rounded bg-white p-4"
                                /* Le SVG vient de notre propre serveur (Fortify). */
                                dangerouslySetInnerHTML={{ __html: qr }}
                            />
                        ) : (
                            <p className="opacity-60">{t('doubleAuthentification.preparationDuCode')}</p>
                        )}

                        <form onSubmit={confirmer} className="max-w-xs space-y-3">
                            <label htmlFor="code" className="block">
                                {t('doubleAuthentification.code')}
                            </label>
                            <input
                                id="code"
                                inputMode="numeric"
                                autoComplete="one-time-code"
                                required
                                value={confirmation.data.code}
                                onChange={(e) => confirmation.setData('code', e.target.value)}
                                className="w-full rounded border border-neutral-300 px-3 py-2 tracking-widest dark:border-neutral-700 dark:bg-neutral-900"
                            />
                            {confirmation.errors.code && (
                                <p className="text-red-600 dark:text-red-400">{confirmation.errors.code}</p>
                            )}
                            <button
                                type="submit"
                                disabled={confirmation.processing}
                                className="rounded bg-neutral-900 px-4 py-2 text-white disabled:opacity-50 dark:bg-neutral-100 dark:text-neutral-900"
                            >
                                {t('doubleAuthentification.confirmer')}
                            </button>
                        </form>
                    </>
                )}

                {activee && (
                    <>
                        <p className="rounded border border-emerald-300 bg-emerald-50 px-4 py-3 text-emerald-900 dark:border-emerald-700 dark:bg-emerald-950 dark:text-emerald-100">
                            {t('doubleAuthentification.active')}
                        </p>

                        {codesDeSecours.length > 0 && (
                            <div>
                                <p className="mb-2 font-medium">{t('doubleAuthentification.codesDeSecours')}</p>
                                <p className="mb-3 opacity-70">
                                    {t('doubleAuthentification.codesDeSecoursExplication')}
                                </p>
                                <ul className="rounded bg-neutral-100 p-4 font-mono text-xs dark:bg-neutral-900">
                                    {codesDeSecours.map((code) => (
                                        <li key={code}>{code}</li>
                                    ))}
                                </ul>
                            </div>
                        )}

                        {!obligatoire && (
                            <button
                                type="button"
                                onClick={() => router.delete('/user/two-factor-authentication')}
                                className="rounded border border-neutral-300 px-4 py-2 dark:border-neutral-700"
                            >
                                {t('doubleAuthentification.desactiver')}
                            </button>
                        )}
                    </>
                )}
            </div>
        </MiseEnPage>
    );
}
