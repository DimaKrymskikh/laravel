import "../scss/main.scss";
import './bootstrap';

import { createApp, h } from 'vue';
import { createPinia } from 'pinia';
import { createInertiaApp } from '@inertiajs/vue3';
import { paginationCatalogStore, paginationAccountStore } from '@/Stores/pagination';

const app = createInertiaApp({
    resolve: name => {
        const pages = import.meta.glob('./Pages/**/*.vue', { eager: true });
        return pages[`./Pages/${name}.vue`];
    },
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(createPinia())
            .provide('paginationCatalog', paginationCatalogStore())
            .provide('paginationAccount', paginationAccountStore())
            .mount(el);
    },
})
