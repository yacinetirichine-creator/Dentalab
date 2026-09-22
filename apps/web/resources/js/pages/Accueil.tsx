import { Head } from '@inertiajs/react';
import { useTraduction } from '@/traductions';

interface Props {
    /** Version affichée en pied de page, fournie par le serveur. */
    version: string;
}

export default function Accueil({ version }: Props) {
    const t = useTraduction();

    return (
        <>
            <Head title="Dentalab" />

            <main className="mx-auto flex min-h-screen max-w-2xl flex-col justify-center gap-6 px-6 py-16">
                <h1 className="text-3xl font-semibold">Dentalab</h1>

                <p className="text-lg">{t('accueil.presentation')}</p>

                <p>
                    {t('accueil.cadrage', {
                        cahier: 'docs/cahier-des-charges.md',
                        plan: 'docs/plan-de-travail.md',
                    })}
                </p>

                <footer className="text-sm opacity-60">{t('accueil.version', { version })}</footer>
            </main>
        </>
    );
}
