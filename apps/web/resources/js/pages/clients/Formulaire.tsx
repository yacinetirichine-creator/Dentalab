import { Head, Link, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import Champ from '@/composants/Champ';
import MiseEnPage from '@/composants/MiseEnPage';
import { useTraduction } from '@/traductions';
import type { Cabinet } from '@/types';

interface Props {
    /** null en création. */
    cabinet: Cabinet | null;
}

export default function Formulaire({ cabinet }: Props) {
    const t = useTraduction();
    const champ = (nom: string) => t(`clients.champs.cabinet.${nom}`);

    const formulaire = useForm({
        raison_sociale: cabinet?.raison_sociale ?? '',
        siret: cabinet?.siret ?? '',
        numero_tva: cabinet?.numero_tva ?? '',
        email: cabinet?.email ?? '',
        telephone: cabinet?.telephone ?? '',
        adresse_ligne_1: cabinet?.adresse_ligne_1 ?? '',
        adresse_ligne_2: cabinet?.adresse_ligne_2 ?? '',
        code_postal: cabinet?.code_postal ?? '',
        ville: cabinet?.ville ?? '',
        pays: cabinet?.pays ?? 'FR',
        delai_reglement_jours: cabinet?.delai_reglement_jours ?? 30,
        notes: cabinet?.notes ?? '',
    });

    const titre = cabinet
        ? t('clients.titreModification', { cabinet: cabinet.raison_sociale })
        : t('clients.titreCreation');

    function soumettre(evenement: FormEvent) {
        evenement.preventDefault();

        if (cabinet) {
            formulaire.put(`/clients/${cabinet.id}`);
        } else {
            formulaire.post('/clients');
        }
    }

    return (
        <MiseEnPage titre={titre}>
            <Head title={titre} />

            <form onSubmit={soumettre} className="max-w-2xl space-y-8">
                <fieldset className="space-y-4">
                    <legend className="mb-2 text-sm font-semibold uppercase tracking-wide opacity-60">
                        {t('clients.sections.identite')}
                    </legend>

                    <Champ
                        id="raison_sociale"
                        etiquette={champ('raison_sociale')}
                        required
                        value={formulaire.data.raison_sociale}
                        onChange={(e) => formulaire.setData('raison_sociale', e.target.value)}
                        erreur={formulaire.errors.raison_sociale}
                    />

                    <div className="grid gap-4 sm:grid-cols-2">
                        <Champ
                            id="siret"
                            etiquette={champ('siret')}
                            inputMode="numeric"
                            value={formulaire.data.siret}
                            onChange={(e) => formulaire.setData('siret', e.target.value)}
                            erreur={formulaire.errors.siret}
                        />
                        <Champ
                            id="numero_tva"
                            etiquette={champ('numero_tva')}
                            value={formulaire.data.numero_tva}
                            onChange={(e) => formulaire.setData('numero_tva', e.target.value)}
                            erreur={formulaire.errors.numero_tva}
                        />
                    </div>
                </fieldset>

                <fieldset className="space-y-4">
                    <legend className="mb-2 text-sm font-semibold uppercase tracking-wide opacity-60">
                        {t('clients.sections.coordonnees')}
                    </legend>

                    <div className="grid gap-4 sm:grid-cols-2">
                        <Champ
                            id="email"
                            type="email"
                            etiquette={champ('email')}
                            value={formulaire.data.email}
                            onChange={(e) => formulaire.setData('email', e.target.value)}
                            erreur={formulaire.errors.email}
                        />
                        <Champ
                            id="telephone"
                            etiquette={champ('telephone')}
                            value={formulaire.data.telephone}
                            onChange={(e) => formulaire.setData('telephone', e.target.value)}
                            erreur={formulaire.errors.telephone}
                        />
                    </div>
                </fieldset>

                <fieldset className="space-y-4">
                    <legend className="mb-2 text-sm font-semibold uppercase tracking-wide opacity-60">
                        {t('clients.sections.facturation')}
                    </legend>

                    <Champ
                        id="adresse_ligne_1"
                        etiquette={champ('adresse_ligne_1')}
                        value={formulaire.data.adresse_ligne_1}
                        onChange={(e) => formulaire.setData('adresse_ligne_1', e.target.value)}
                        erreur={formulaire.errors.adresse_ligne_1}
                    />
                    <Champ
                        id="adresse_ligne_2"
                        etiquette={champ('adresse_ligne_2')}
                        value={formulaire.data.adresse_ligne_2}
                        onChange={(e) => formulaire.setData('adresse_ligne_2', e.target.value)}
                        erreur={formulaire.errors.adresse_ligne_2}
                    />

                    <div className="grid gap-4 sm:grid-cols-3">
                        <Champ
                            id="code_postal"
                            etiquette={champ('code_postal')}
                            value={formulaire.data.code_postal}
                            onChange={(e) => formulaire.setData('code_postal', e.target.value)}
                            erreur={formulaire.errors.code_postal}
                        />
                        <Champ
                            id="ville"
                            etiquette={champ('ville')}
                            value={formulaire.data.ville}
                            onChange={(e) => formulaire.setData('ville', e.target.value)}
                            erreur={formulaire.errors.ville}
                        />
                        <Champ
                            id="pays"
                            etiquette={champ('pays')}
                            maxLength={2}
                            value={formulaire.data.pays}
                            onChange={(e) => formulaire.setData('pays', e.target.value.toUpperCase())}
                            erreur={formulaire.errors.pays}
                        />
                    </div>
                </fieldset>

                <fieldset className="space-y-4">
                    <legend className="mb-2 text-sm font-semibold uppercase tracking-wide opacity-60">
                        {t('clients.sections.reglement')}
                    </legend>

                    <Champ
                        id="delai_reglement_jours"
                        type="number"
                        min={0}
                        max={365}
                        etiquette={champ('delai_reglement_jours')}
                        aide={t('clients.aides.delai_reglement_jours')}
                        value={formulaire.data.delai_reglement_jours}
                        onChange={(e) => formulaire.setData('delai_reglement_jours', Number(e.target.value))}
                        erreur={formulaire.errors.delai_reglement_jours}
                    />
                </fieldset>

                <fieldset className="space-y-4">
                    <legend className="mb-2 text-sm font-semibold uppercase tracking-wide opacity-60">
                        {t('clients.sections.notes')}
                    </legend>

                    <Champ
                        id="notes"
                        etiquette={champ('notes')}
                        erreur={formulaire.errors.notes}
                        enfant={
                            <textarea
                                id="notes"
                                rows={4}
                                value={formulaire.data.notes}
                                onChange={(e) => formulaire.setData('notes', e.target.value)}
                                className="w-full rounded border border-neutral-300 px-3 py-2 dark:border-neutral-700 dark:bg-neutral-900"
                            />
                        }
                    />
                </fieldset>

                <div className="flex items-center gap-4">
                    <button
                        type="submit"
                        disabled={formulaire.processing}
                        className="rounded bg-neutral-900 px-4 py-2 text-white disabled:opacity-50 dark:bg-neutral-100 dark:text-neutral-900"
                    >
                        {t('clients.enregistrer')}
                    </button>

                    <Link
                        href={cabinet ? `/clients/${cabinet.id}` : '/clients'}
                        className="text-sm underline opacity-70"
                    >
                        {t('clients.annuler')}
                    </Link>
                </div>
            </form>
        </MiseEnPage>
    );
}
