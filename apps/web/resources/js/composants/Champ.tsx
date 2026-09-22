import type { InputHTMLAttributes, ReactNode } from 'react';

interface Props extends InputHTMLAttributes<HTMLInputElement> {
    etiquette: string;
    erreur?: string;
    aide?: string;
    enfant?: ReactNode;
}

/**
 * Un champ de formulaire : étiquette, saisie, aide, erreur.
 *
 * Aucun texte ici — tout arrive par les propriétés, elles-mêmes traduites
 * par l'écran appelant.
 */
export default function Champ({ etiquette, erreur, aide, enfant, id, ...reste }: Props) {
    return (
        <div>
            <label htmlFor={id} className="mb-1 block text-sm font-medium">
                {etiquette}
            </label>

            {enfant ?? (
                <input
                    id={id}
                    {...reste}
                    className="w-full rounded border border-neutral-300 px-3 py-2 dark:border-neutral-700 dark:bg-neutral-900"
                />
            )}

            {aide && <p className="mt-1 text-xs opacity-60">{aide}</p>}
            {erreur && <p className="mt-1 text-sm text-red-600 dark:text-red-400">{erreur}</p>}
        </div>
    );
}
