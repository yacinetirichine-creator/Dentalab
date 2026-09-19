import { createInertiaApp } from '@inertiajs/react';
import { createRoot } from 'react-dom/client';
import '../css/app.css';

createInertiaApp({
    title: (titre) => (titre ? `${titre} — Dentalab` : 'Dentalab'),

    resolve: (nom) => {
        const pages = import.meta.glob('./pages/**/*.tsx', { eager: true });

        return pages[`./pages/${nom}.tsx`] as never;
    },

    setup({ el, App, props }) {
        createRoot(el).render(<App {...props} />);
    },
});
