import adapter from '@sveltejs/adapter-static';
import { vitePreprocess } from '@sveltejs/vite-plugin-svelte';

const config = {
  preprocess: vitePreprocess(),

  kit: {
    adapter: adapter({
      fallback: 'index.html',
      strict: false
    }),

    paths: {
      base: ''
    },

    prerender: {
      entries: ['*']
    },
    // vite: {
    //   optimizeDeps: {
    //     include: ['just-throttle','dayjs']
    //   }
    // }

  }
};

export default config;
