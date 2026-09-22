import { Head, useForm } from '@inertiajs/react';
import { type FormEvent, useState } from 'react';
import { useTraduction } from '@/traductions';

/**
 * Deuxième étape de la connexion : le code à six chiffres, ou un code de
 * secours si l'utilisateur n'a plus son téléphone.
 */
export default function DoubleAuthentificationDefi() {
    const t = useTraduction();
    const [codeDeSecours, setCodeDeSecours] = useState(false);
    const formulaire = useForm({ code: '', recovery_code: '' });

    function soumettre(evenement: FormEvent) {
        evenement.preventDefault();
        formulaire.post('/two-factor-challenge');
    }

    return (
        <div className="flex min-h-screen items-center justify-center bg-neutral-50 px-6 py-12 text-neutral-900 dark:bg-neutral-950 dark:text-neutral-100">
            <Head title={t('defi.titre')} />

            <div className="w-full max-w-sm">
                <h1 className="mb-1 text-2xl font-semibold">{t('defi.titre')}</h1>
                <p className="mb-8 text-sm opacity-60">
                    {codeDeSecours ? t('defi.consigneSecours') : t('defi.consigneApplication')}
                </p>

                <form onSubmit={soumettre} className="space-y-4">
                    {codeDeSecours ? (
                        <div>
                            <label htmlFor="recovery_code" className="mb-1 block text-sm">
                                {t('defi.codeDeSecours')}
                            </label>
                            <input
                                id="recovery_code"
                                autoComplete="one-time-code"
                                required
                                autoFocus
                                value={formulaire.data.recovery_code}
                                onChange={(e) => formulaire.setData('recovery_code', e.target.value)}
                                className="w-full rounded border border-neutral-300 px-3 py-2 dark:border-neutral-700 dark:bg-neutral-900"
                            />
                            {formulaire.errors.recovery_code && (
                                <p className="mt-1 text-sm text-red-600 dark:text-red-400">
                                    {formulaire.errors.recovery_code}
                                </p>
                            )}
                        </div>
                    ) : (
                        <div>
                            <label htmlFor="code" className="mb-1 block text-sm">
                                {t('defi.code')}
                            </label>
                            <input
                                id="code"
                                inputMode="numeric"
                                autoComplete="one-time-code"
                                required
                                autoFocus
                                value={formulaire.data.code}
                                onChange={(e) => formulaire.setData('code', e.target.value)}
                                className="w-full rounded border border-neutral-300 px-3 py-2 tracking-widest dark:border-neutral-700 dark:bg-neutral-900"
                            />
                            {formulaire.errors.code && (
                                <p className="mt-1 text-sm text-red-600 dark:text-red-400">{formulaire.errors.code}</p>
                            )}
                        </div>
                    )}

                    <button
                        type="submit"
                        disabled={formulaire.processing}
                        className="w-full rounded bg-neutral-900 px-4 py-2 text-white disabled:opacity-50 dark:bg-neutral-100 dark:text-neutral-900"
                    >
                        {t('defi.verifier')}
                    </button>

                    <button
                        type="button"
                        onClick={() => setCodeDeSecours(!codeDeSecours)}
                        className="w-full text-sm underline opacity-70"
                    >
                        {codeDeSecours ? t('defi.utiliserApplication') : t('defi.utiliserSecours')}
                    </button>
                </form>
            </div>
        </div>
    );
}
