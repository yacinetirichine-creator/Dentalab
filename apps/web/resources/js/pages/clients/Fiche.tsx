import { Head, Link, router, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import Champ from '@/composants/Champ';
import MiseEnPage from '@/composants/MiseEnPage';
import { useTraduction } from '@/traductions';
import type { AdresseLivraison, Cabinet, Praticien } from '@/types';

interface Props {
    cabinet: Cabinet;
}

export default function Fiche({ cabinet }: Props) {
    const t = useTraduction();

    const praticiens = (cabinet.praticiens ?? []).filter((p) => p.archive_le === null);
    const adresses = (cabinet.adresses_livraison ?? []).filter((a) => a.archive_le === null);

    return (
        <MiseEnPage titre={cabinet.raison_sociale}>
            <Head title={cabinet.raison_sociale} />

            <div className="space-y-10">
                {cabinet.archive_le && (
                    <p className="rounded border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-900 dark:border-amber-700 dark:bg-amber-950 dark:text-amber-100">
                        {t('clients.estArchive')}
                    </p>
                )}

                <div className="flex flex-wrap items-center gap-4 text-sm">
                    <Link href={`/clients/${cabinet.id}/modifier`} className="underline">
                        {t('clients.modifier')}
                    </Link>

                    {cabinet.archive_le ? (
                        <button
                            type="button"
                            onClick={() => router.post(`/clients/${cabinet.id}/restaurer`)}
                            className="underline opacity-70"
                        >
                            {t('clients.restaurer')}
                        </button>
                    ) : (
                        <button
                            type="button"
                            onClick={() => {
                                if (window.confirm(t('clients.confirmationArchivage'))) {
                                    router.post(`/clients/${cabinet.id}/archiver`);
                                }
                            }}
                            className="underline opacity-70"
                        >
                            {t('clients.archiver')}
                        </button>
                    )}

                    <Link href="/clients" className="underline opacity-70">
                        {t('clients.retourALaListe')}
                    </Link>
                </div>

                <Coordonnees cabinet={cabinet} />
                <SectionPraticiens cabinet={cabinet} praticiens={praticiens} />
                <SectionAdresses cabinet={cabinet} adresses={adresses} />
            </div>
        </MiseEnPage>
    );
}

function Coordonnees({ cabinet }: { cabinet: Cabinet }) {
    const t = useTraduction();
    const champ = (nom: string) => t(`clients.champs.cabinet.${nom}`);

    const lignes: Array<[string, string | number | null]> = [
        [champ('siret'), cabinet.siret],
        [champ('numero_tva'), cabinet.numero_tva],
        [champ('email'), cabinet.email],
        [champ('telephone'), cabinet.telephone],
        [
            champ('adresse_ligne_1'),
            [cabinet.adresse_ligne_1, cabinet.adresse_ligne_2, cabinet.code_postal, cabinet.ville]
                .filter(Boolean)
                .join(', ') || null,
        ],
        [champ('delai_reglement_jours'), cabinet.delai_reglement_jours],
    ];

    return (
        <section>
            <h2 className="mb-3 text-sm font-semibold uppercase tracking-wide opacity-60">
                {t('clients.sections.coordonnees')}
            </h2>

            <dl className="grid gap-x-8 gap-y-2 text-sm sm:grid-cols-2">
                {lignes
                    .filter(([, valeur]) => valeur !== null && valeur !== '')
                    .map(([etiquette, valeur]) => (
                        <div key={etiquette} className="flex gap-2">
                            <dt className="opacity-60">{etiquette}</dt>
                            <dd>{valeur}</dd>
                        </div>
                    ))}
            </dl>

            {cabinet.notes && <p className="mt-4 whitespace-pre-line text-sm opacity-80">{cabinet.notes}</p>}
        </section>
    );
}

function SectionPraticiens({ cabinet, praticiens }: { cabinet: Cabinet; praticiens: Praticien[] }) {
    const t = useTraduction();
    const champ = (nom: string) => t(`clients.champs.praticien.${nom}`);
    const formulaire = useForm({ civilite: '', nom: '', prenom: '', numero_rpps: '', email: '', telephone: '' });

    function ajouter(evenement: FormEvent) {
        evenement.preventDefault();
        formulaire.post(`/clients/${cabinet.id}/praticiens`, { onSuccess: () => formulaire.reset() });
    }

    return (
        <section>
            <h2 className="mb-3 text-sm font-semibold uppercase tracking-wide opacity-60">
                {t('clients.sections.praticiens')}
            </h2>

            {praticiens.length === 0 ? (
                <p className="mb-4 text-sm opacity-60">{t('clients.aucunPraticien')}</p>
            ) : (
                <ul className="mb-6 divide-y divide-neutral-200 rounded border border-neutral-200 text-sm dark:divide-neutral-800 dark:border-neutral-800">
                    {praticiens.map((praticien) => (
                        <li key={praticien.id} className="flex flex-wrap items-baseline justify-between gap-2 px-4 py-3">
                            <span>
                                {[praticien.civilite, praticien.prenom, praticien.nom].filter(Boolean).join(' ')}
                            </span>
                            <span className="flex items-baseline gap-4 opacity-60">
                                {praticien.numero_rpps}
                                <button
                                    type="button"
                                    onClick={() =>
                                        router.post(`/clients/${cabinet.id}/praticiens/${praticien.id}/archiver`)
                                    }
                                    className="underline"
                                >
                                    {t('clients.archiver')}
                                </button>
                            </span>
                        </li>
                    ))}
                </ul>
            )}

            <form onSubmit={ajouter} className="grid max-w-2xl gap-4 sm:grid-cols-3">
                <Champ
                    id="praticien_civilite"
                    etiquette={champ('civilite')}
                    value={formulaire.data.civilite}
                    onChange={(e) => formulaire.setData('civilite', e.target.value)}
                    erreur={formulaire.errors.civilite}
                />
                <Champ
                    id="praticien_prenom"
                    etiquette={champ('prenom')}
                    value={formulaire.data.prenom}
                    onChange={(e) => formulaire.setData('prenom', e.target.value)}
                    erreur={formulaire.errors.prenom}
                />
                <Champ
                    id="praticien_nom"
                    etiquette={champ('nom')}
                    required
                    value={formulaire.data.nom}
                    onChange={(e) => formulaire.setData('nom', e.target.value)}
                    erreur={formulaire.errors.nom}
                />
                <Champ
                    id="praticien_rpps"
                    etiquette={champ('numero_rpps')}
                    aide={t('clients.aides.numero_rpps')}
                    inputMode="numeric"
                    value={formulaire.data.numero_rpps}
                    onChange={(e) => formulaire.setData('numero_rpps', e.target.value)}
                    erreur={formulaire.errors.numero_rpps}
                />
                <Champ
                    id="praticien_email"
                    type="email"
                    etiquette={champ('email')}
                    value={formulaire.data.email}
                    onChange={(e) => formulaire.setData('email', e.target.value)}
                    erreur={formulaire.errors.email}
                />
                <Champ
                    id="praticien_telephone"
                    etiquette={champ('telephone')}
                    value={formulaire.data.telephone}
                    onChange={(e) => formulaire.setData('telephone', e.target.value)}
                    erreur={formulaire.errors.telephone}
                />

                <div className="sm:col-span-3">
                    <button
                        type="submit"
                        disabled={formulaire.processing}
                        className="rounded border border-neutral-300 px-4 py-2 text-sm disabled:opacity-50 dark:border-neutral-700"
                    >
                        {t('clients.ajouter')}
                    </button>
                </div>
            </form>
        </section>
    );
}

function SectionAdresses({ cabinet, adresses }: { cabinet: Cabinet; adresses: AdresseLivraison[] }) {
    const t = useTraduction();
    const champ = (nom: string) => t(`clients.champs.adresse.${nom}`);
    const formulaire = useForm({
        libelle: '',
        adresse_ligne_1: '',
        adresse_ligne_2: '',
        code_postal: '',
        ville: '',
        pays: 'FR',
        contact: '',
        telephone: '',
        instructions: '',
        est_principale: false,
    });

    function ajouter(evenement: FormEvent) {
        evenement.preventDefault();
        formulaire.post(`/clients/${cabinet.id}/adresses`, { onSuccess: () => formulaire.reset() });
    }

    return (
        <section>
            <h2 className="mb-3 text-sm font-semibold uppercase tracking-wide opacity-60">
                {t('clients.sections.adresses')}
            </h2>

            {adresses.length === 0 ? (
                <p className="mb-4 text-sm opacity-60">{t('clients.aucuneAdresse')}</p>
            ) : (
                <ul className="mb-6 divide-y divide-neutral-200 rounded border border-neutral-200 text-sm dark:divide-neutral-800 dark:border-neutral-800">
                    {adresses.map((adresse) => (
                        <li key={adresse.id} className="flex flex-wrap items-baseline justify-between gap-2 px-4 py-3">
                            <span>
                                <span className="font-medium">{adresse.libelle}</span>
                                <span className="opacity-60">
                                    {' · '}
                                    {[adresse.adresse_ligne_1, adresse.code_postal, adresse.ville]
                                        .filter(Boolean)
                                        .join(', ')}
                                </span>
                            </span>
                            <span className="flex items-baseline gap-4 opacity-60">
                                {adresse.est_principale && <span>{t('clients.adressePrincipale')}</span>}
                                <button
                                    type="button"
                                    onClick={() =>
                                        router.post(`/clients/${cabinet.id}/adresses/${adresse.id}/archiver`)
                                    }
                                    className="underline"
                                >
                                    {t('clients.archiver')}
                                </button>
                            </span>
                        </li>
                    ))}
                </ul>
            )}

            <form onSubmit={ajouter} className="grid max-w-2xl gap-4 sm:grid-cols-3">
                <div className="sm:col-span-3">
                    <Champ
                        id="adresse_libelle"
                        etiquette={champ('libelle')}
                        required
                        value={formulaire.data.libelle}
                        onChange={(e) => formulaire.setData('libelle', e.target.value)}
                        erreur={formulaire.errors.libelle}
                    />
                </div>

                <div className="sm:col-span-3">
                    <Champ
                        id="adresse_ligne_1"
                        etiquette={champ('adresse_ligne_1')}
                        required
                        value={formulaire.data.adresse_ligne_1}
                        onChange={(e) => formulaire.setData('adresse_ligne_1', e.target.value)}
                        erreur={formulaire.errors.adresse_ligne_1}
                    />
                </div>

                <Champ
                    id="adresse_code_postal"
                    etiquette={champ('code_postal')}
                    required
                    value={formulaire.data.code_postal}
                    onChange={(e) => formulaire.setData('code_postal', e.target.value)}
                    erreur={formulaire.errors.code_postal}
                />
                <Champ
                    id="adresse_ville"
                    etiquette={champ('ville')}
                    required
                    value={formulaire.data.ville}
                    onChange={(e) => formulaire.setData('ville', e.target.value)}
                    erreur={formulaire.errors.ville}
                />
                <Champ
                    id="adresse_contact"
                    etiquette={champ('contact')}
                    value={formulaire.data.contact}
                    onChange={(e) => formulaire.setData('contact', e.target.value)}
                    erreur={formulaire.errors.contact}
                />

                <div className="sm:col-span-3">
                    <Champ
                        id="adresse_instructions"
                        etiquette={champ('instructions')}
                        aide={t('clients.aides.instructions')}
                        erreur={formulaire.errors.instructions}
                        enfant={
                            <textarea
                                id="adresse_instructions"
                                rows={2}
                                value={formulaire.data.instructions}
                                onChange={(e) => formulaire.setData('instructions', e.target.value)}
                                className="w-full rounded border border-neutral-300 px-3 py-2 dark:border-neutral-700 dark:bg-neutral-900"
                            />
                        }
                    />
                </div>

                <label className="flex items-center gap-2 text-sm sm:col-span-3">
                    <input
                        type="checkbox"
                        checked={formulaire.data.est_principale}
                        onChange={(e) => formulaire.setData('est_principale', e.target.checked)}
                    />
                    {champ('est_principale')}
                </label>

                <div className="sm:col-span-3">
                    <button
                        type="submit"
                        disabled={formulaire.processing}
                        className="rounded border border-neutral-300 px-4 py-2 text-sm disabled:opacity-50 dark:border-neutral-700"
                    >
                        {t('clients.ajouter')}
                    </button>
                </div>
            </form>
        </section>
    );
}
