import { Head } from '@inertiajs/react';
import MiseEnPage from '@/composants/MiseEnPage';
import { useTraduction } from '@/traductions';

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
    const t = useTraduction();

    return (
        <MiseEnPage titre={titre}>
            <Head title={titre} />

            <p className="max-w-xl text-sm leading-relaxed opacity-70">
                {t('chantier.explication', { lot })}
            </p>
        </MiseEnPage>
    );
}
