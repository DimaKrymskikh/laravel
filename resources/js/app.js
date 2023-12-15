import "../scss/main.scss";
import './bootstrap';

import { createApp, h } from 'vue';
import { createPinia } from 'pinia';
import { createInertiaApp } from '@inertiajs/vue3';
import { useAppStore } from '@/Stores/app';
import { useActorsListStore } from '@/Stores/actors';
import { useFilmsListStore, useFilmsAccountStore } from '@/Stores/films';

const app = createInertiaApp({
    resolve: name => {
        const pages = import.meta.glob('./Pages/**/*.vue', { eager: true });
        return pages[`./Pages/${name}.vue`];
    },
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(createPinia())
            .provide('app', useAppStore())
            .provide('actorsList', useActorsListStore())
            .provide('filmsList', useFilmsListStore())
            .provide('filmsAccount', useFilmsAccountStore())
            .mount(el);
    },
});
