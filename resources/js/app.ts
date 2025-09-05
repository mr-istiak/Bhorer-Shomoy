import '../css/app.css';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createApp, defineAsyncComponent, h as vueH } from 'vue';
import { ZiggyVue } from 'ziggy-js';
import { initializeTheme } from './composables/useAppearance';

const AppLayout = defineAsyncComponent(() => import('@/layouts/AppLayout.vue'))

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),

    resolve: (name) => {
        const page = resolvePageComponent(`./pages/${name}.vue`, import.meta.glob<DefineComponent>('./pages/**/*.vue'));
        page.then((module: any) => {
            module.default.layout = module.default.layout || ((h: any, page: any) => h(
                AppLayout,
                {
                    breadcrumbs: module.default.breadcrumbs || [],
                    messages: page.props.messages,
                    errors: page.props.errors
                },
                { default: () => page }
            ));
        });
        return page;
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => vueH(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: 'hsl(173 58% 39%)',
    },
});

// This will set light / dark mode on page load...
initializeTheme();
