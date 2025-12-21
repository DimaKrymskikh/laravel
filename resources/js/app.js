import "../scss/main.scss";
import './bootstrap';

import { createApp, h } from 'vue';
import { createPinia } from 'pinia';
import { createInertiaApp } from '@inertiajs/vue3';
import { useActorsListStore } from '@/Stores/actors';
import { useFilmsListStore, useFilmsAccountStore, useFilmsAdminStore } from '@/Stores/films';
import { useLanguagesListStore } from '@/Stores/languages';

const app = createInertiaApp({
    progress: false,
    resolve: name => {
        const pages = import.meta.glob('./Pages/**/*.vue', { eager: true });
        return pages[`./Pages/${name}.vue`];
    },
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(createPinia())
            .provide('actorsList', useActorsListStore())
            .provide('filmsList', useFilmsListStore())
            .provide('filmsAccount', useFilmsAccountStore())
            .provide('filmsAdmin', useFilmsAdminStore())
            .provide('languagesList', useLanguagesListStore())
            .mount(el);
    }
});
