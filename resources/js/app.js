import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import reveal from './directives/reveal';

createInertiaApp({
    title: (title) => (title ? `${title}` : document.title),
    resolve: (name) => {
        const pages = import.meta.glob('./Pages/**/*.vue');
        const page = pages[`./Pages/${name}.vue`];
        if (!page) throw new Error(`Page not found: ${name}`);
        return page();
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .directive('reveal', reveal)
            .mount(el);
    },
    progress: {
        color: '#d4af7a',
        showSpinner: false,
    },
});
