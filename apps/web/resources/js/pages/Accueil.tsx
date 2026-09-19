import { Head } from '@inertiajs/react';

interface Props {
    /** Version affichée en pied de page, fournie par le serveur. */
    version: string;
}

export default function Accueil({ version }: Props) {
    return (
        <>
            <Head title="Accueil" />

            <main className="mx-auto flex min-h-screen max-w-2xl flex-col justify-center gap-6 px-6 py-16">
                <h1 className="text-3xl font-semibold">Dentalab</h1>

                <p className="text-lg">
                    Plateforme de gestion pour laboratoires de prothèses dentaires :
                    commandes, fabrication, traçabilité, livraison et facturation.
                </p>

                <p>
                    Le projet est en phase de cadrage. Le périmètre et le découpage en
                    lots sont décrits dans <code>docs/cahier-des-charges.md</code> et{' '}
                    <code>docs/plan-de-travail.md</code>.
                </p>

                <footer className="text-sm opacity-60">Version {version}</footer>
            </main>
        </>
    );
}
