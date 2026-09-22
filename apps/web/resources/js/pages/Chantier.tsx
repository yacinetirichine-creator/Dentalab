import { Head } from '@inertiajs/react';
import MiseEnPage from '@/composants/MiseEnPage';

interface Props {
    titre: string;
    /** Le lot du plan de travail qui construira cet écran. */
    lot: string;
}

/**
 * Écran pas encore construit.
 *
 * Ces routes existent dès maintenant pour que les droits de chaque rôle
 * soient réellement testés, et pour que le menu corresponde à ce que chacun
 * pourra faire.
 */
export default function Chantier({ titre, lot }: Props) {
    return (
        <MiseEnPage titre={titre}>
            <Head title={titre} />

            <p className="max-w-xl text-sm leading-relaxed opacity-70">
                Cet écran reste à construire : il fait partie du lot {lot} du plan de travail.
                Vous y avez accès, c’est déjà ce qui est vérifié ici.
            </p>
        </MiseEnPage>
    );
}
