import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import '../css/app.css';

createInertiaApp({
    title: (title) => title ? `${title} - CBT SaaS` : 'CBT SaaS',
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });

        // Global mixins for exam time checking
        app.mixin({
            methods: {
                examTimeRangeChecker(start_time, end_time) {
                    return new Date() >= new Date(start_time) && new Date() <= new Date(end_time);
                },
                examTimeStartChecker(start_time) {
                    return new Date() < new Date(start_time);
                },
                examTimeEndChecker(end_time) {
                    return new Date() > new Date(end_time);
                }
            },
        });

        app.use(plugin).mount(el);
    },
    progress: {
        color: '#4B5563',
        showSpinner: true,
    },
});
