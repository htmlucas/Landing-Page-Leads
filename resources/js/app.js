import './bootstrap';

import { createApp, h } from 'vue'
import { createInertiaApp, Head, Link } from '@inertiajs/vue3'
import Layout from './Layouts/Layout.vue'
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import VueTheMask from 'vue-the-mask'

createInertiaApp({
  title: (title) => `My App ${title}`,
  resolve: name => {
    const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
    let page = pages[`./Pages/${name}.vue`];
    page.default.layout = page.default.layout || Layout;
    return page;
  },
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(ZiggyVue)
      .use(VueTheMask)
      .component('Head', Head)
      .component('Link', Link)
      .mount(el)
  },
  progress : {
    dealy: 250,
    color: '#fff',
    includeCSS: true,
    showSpinner: true,
  }
})