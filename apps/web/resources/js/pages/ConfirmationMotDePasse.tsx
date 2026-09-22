import { Head, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { useTraduction } from '@/traductions';

/**
 * Redemande le mot de passe avant une opération sensible (activation ou
 * désactivation de la double authentification).
 */
export default function ConfirmationMotDePasse() {
    const t = useTraduction();
    const formulaire = useForm({ password: '' });

    function soumettre(evenement: FormEvent) {
        evenement.preventDefault();
        formulaire.post('/user/confirm-password', {
            onFinish: () => formulaire.reset('password'),
        });
    }

    return (
        <div className="flex min-h-screen items-center justify-center bg-neutral-50 px-6 py-12 text-neutral-900 dark:bg-neutral-950 dark:text-neutral-100">
            <Head title={t('confirmationMotDePasse.confirmer')} />

            <div className="w-full max-w-sm">
                <h1 className="mb-1 text-2xl font-semibold">{t('confirmationMotDePasse.titre')}</h1>
                <p className="mb-8 text-sm opacity-60">{t('confirmationMotDePasse.explication')}</p>

                <form onSubmit={soumettre} className="space-y-4">
                    <div>
                        <label htmlFor="password" className="mb-1 block text-sm">
                            {t('confirmationMotDePasse.motDePasse')}
                        </label>
                        <input
                            id="password"
                            type="password"
                            autoComplete="current-password"
                            required
                            autoFocus
                            value={formulaire.data.password}
                            onChange={(e) => formulaire.setData('password', e.target.value)}
                            className="w-full rounded border border-neutral-300 px-3 py-2 dark:border-neutral-700 dark:bg-neutral-900"
                        />
                        {formulaire.errors.password && (
                            <p className="mt-1 text-sm text-red-600 dark:text-red-400">{formulaire.errors.password}</p>
                        )}
                    </div>

                    <button
                        type="submit"
                        disabled={formulaire.processing}
                        className="w-full rounded bg-neutral-900 px-4 py-2 text-white disabled:opacity-50 dark:bg-neutral-100 dark:text-neutral-900"
                    >
                        {t('confirmationMotDePasse.confirmer')}
                    </button>
                </form>
            </div>
        </div>
    );
}
