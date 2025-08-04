import { createRoot } from 'react-dom/client';
import { createInertiaApp } from '@inertiajs/inertia-react';
import { InertiaProgress } from '@inertiajs/progress';

createInertiaApp({
  resolve: name => require(`./Pages/${name}`).default,
  setup({ el, App, props }) {
    createRoot(el).render(<App {...props} />);
  },
});

InertiaProgress.init();
