import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

// La marque vient du serveur (balise og:site_name) : elle suit le nom saisi dans l'admin.
const siteName = () =>
    document.head.querySelector('meta[property="og:site_name"]')?.content || appName;

createInertiaApp({
    // Un titre SEO complet (« Nom | Marque ») est utilisé tel quel ; les autres pages reçoivent « Titre | Marque ».
    title: (title) => (!title ? siteName() : (title.includes('|') ? title : `${title} | ${siteName()}`)),
    resolve: (name) =>
        resolvePageComponent(
            `./pages/${name}.vue`,
            import.meta.glob('./pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
