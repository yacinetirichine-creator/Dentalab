import { Head, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';

interface Props {
    statut?: string | null;
}

export default function Connexion({ statut }: Props) {
    const formulaire = useForm({ email: '', password: '', remember: false });

    function soumettre(evenement: FormEvent) {
        evenement.preventDefault();
        formulaire.post('/login', { onFinish: () => formulaire.reset('password') });
    }

    return (
        <div className="flex min-h-screen items-center justify-center bg-neutral-50 px-6 py-12 text-neutral-900 dark:bg-neutral-950 dark:text-neutral-100">
            <Head title="Connexion" />

            <div className="w-full max-w-sm">
                <h1 className="mb-1 text-2xl font-semibold">Dentalab</h1>
                <p className="mb-8 text-sm opacity-60">Connectez-vous à votre laboratoire.</p>

                {statut && <p className="mb-4 text-sm text-emerald-700 dark:text-emerald-400">{statut}</p>}

                <form onSubmit={soumettre} className="space-y-4">
                    <div>
                        <label htmlFor="email" className="mb-1 block text-sm">Adresse e-mail</label>
                        <input
                            id="email"
                            type="email"
                            autoComplete="username"
                            required
                            autoFocus
                            value={formulaire.data.email}
                            onChange={(e) => formulaire.setData('email', e.target.value)}
                            className="w-full rounded border border-neutral-300 px-3 py-2 dark:border-neutral-700 dark:bg-neutral-900"
                        />
                        {formulaire.errors.email && (
                            <p className="mt-1 text-sm text-red-600 dark:text-red-400">{formulaire.errors.email}</p>
                        )}
                    </div>

                    <div>
                        <label htmlFor="password" className="mb-1 block text-sm">Mot de passe</label>
                        <input
                            id="password"
                            type="password"
                            autoComplete="current-password"
                            required
                            value={formulaire.data.password}
                            onChange={(e) => formulaire.setData('password', e.target.value)}
                            className="w-full rounded border border-neutral-300 px-3 py-2 dark:border-neutral-700 dark:bg-neutral-900"
                        />
                    </div>

                    <label className="flex items-center gap-2 text-sm">
                        <input
                            type="checkbox"
                            checked={formulaire.data.remember}
                            onChange={(e) => formulaire.setData('remember', e.target.checked)}
                        />
                        Rester connecté
                    </label>

                    <button
                        type="submit"
                        disabled={formulaire.processing}
                        className="w-full rounded bg-neutral-900 px-4 py-2 text-white disabled:opacity-50 dark:bg-neutral-100 dark:text-neutral-900"
                    >
                        Se connecter
                    </button>
                </form>
            </div>
        </div>
    );
}
