import { Head, router, useForm } from '@inertiajs/react';
import { type FormEvent, useEffect, useState } from 'react';
import MiseEnPage from '@/composants/MiseEnPage';

interface Props {
    /** Le rôle de l'utilisateur impose-t-il la double authentification ? */
    obligatoire: boolean;
    /** Activée et confirmée. */
    activee: boolean;
    /** Activée mais pas encore confirmée par un premier code. */
    enAttenteDeConfirmation: boolean;
}

export default function DoubleAuthentification({ obligatoire, activee, enAttenteDeConfirmation }: Props) {
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
        <MiseEnPage titre="Double authentification">
            <Head title="Double authentification" />

            <div className="max-w-xl space-y-6 text-sm leading-relaxed">
                {obligatoire && !activee && (
                    <p className="rounded border border-amber-300 bg-amber-50 px-4 py-3 text-amber-900 dark:border-amber-700 dark:bg-amber-950 dark:text-amber-100">
                        Votre rôle donne accès à la facturation et aux paramètres du laboratoire.
                        La double authentification est obligatoire pour continuer.
                    </p>
                )}

                {!activee && !enAttenteDeConfirmation && (
                    <>
                        <p>
                            Un code à six chiffres, changeant toutes les trente secondes, vous sera
                            demandé à chaque connexion. Il faut une application d’authentification
                            sur votre téléphone.
                        </p>
                        <button
                            type="button"
                            onClick={() => router.post('/user/two-factor-authentication')}
                            className="rounded bg-neutral-900 px-4 py-2 text-white dark:bg-neutral-100 dark:text-neutral-900"
                        >
                            Activer la double authentification
                        </button>
                    </>
                )}

                {enAttenteDeConfirmation && (
                    <>
                        <p>
                            Scannez ce code avec votre application d’authentification, puis saisissez
                            le code à six chiffres qu’elle affiche.
                        </p>

                        {qr ? (
                            <div
                                className="inline-block rounded bg-white p-4"
                                /* Le SVG vient de notre propre serveur (Fortify). */
                                dangerouslySetInnerHTML={{ __html: qr }}
                            />
                        ) : (
                            <p className="opacity-60">Préparation du code…</p>
                        )}

                        <form onSubmit={confirmer} className="max-w-xs space-y-3">
                            <label htmlFor="code" className="block">Code à six chiffres</label>
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
                                Confirmer
                            </button>
                        </form>
                    </>
                )}

                {activee && (
                    <>
                        <p className="rounded border border-emerald-300 bg-emerald-50 px-4 py-3 text-emerald-900 dark:border-emerald-700 dark:bg-emerald-950 dark:text-emerald-100">
                            La double authentification est active sur votre compte.
                        </p>

                        {codesDeSecours.length > 0 && (
                            <div>
                                <p className="mb-2 font-medium">Codes de secours</p>
                                <p className="mb-3 opacity-70">
                                    Conservez-les hors de votre téléphone. Ils sont votre seul moyen
                                    d’entrer si vous le perdez, et chacun ne sert qu’une fois.
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
                                Désactiver
                            </button>
                        )}
                    </>
                )}
            </div>
        </MiseEnPage>
    );
}
