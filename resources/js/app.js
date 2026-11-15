import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';

const pages = import.meta.glob('./Pages/**/*.vue');

createInertiaApp({
    title: (title) => `${title} · Archivo Galáctico`,
    resolve: (name) => pages[`./Pages/${name}.vue`](),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) }).use(plugin).mount(el);
    },
});
