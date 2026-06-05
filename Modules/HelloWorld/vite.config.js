import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import { resolve } from 'path'

/**
 * Module build config.
 *
 * Produces a single `resources/dist/init.js` ES module that the host app
 * loads via <script type="module">. Vue is externalized and resolved from
 * the host's `window.__invoiceshelf_vue` global at runtime.
 *
 * The vueGlobalPlugin rewrites `import { ref, ... } from "vue"` into
 * destructuring from the host global — no separate Vue bundle, no import
 * map, and the module shares the host's Vue instance so globally
 * registered Base* components are accessible via resolveComponent().
 */

/**
 * Vite plugin that replaces Vue imports with the host's global at runtime.
 * Works by marking 'vue' as external and then rewriting the import in the
 * output via renderChunk.
 */
function vueGlobalPlugin() {
  return {
    name: 'invoiceshelf-vue-global',
    enforce: 'pre',
    resolveId(source) {
      if (source === 'vue') {
        return { id: 'vue', external: true }
      }
    },
    renderChunk(code) {
      // Replace: import { ref, computed, ... } from "vue";
      // With:    const { ref, computed, ... } = window.__invoiceshelf_vue;
      return code.replace(
        /import\s*(\{[^}]+\})\s*from\s*"vue"\s*;?/g,
        'const $1 = window.__invoiceshelf_vue;'
      )
    },
  }
}

export default defineConfig({
  build: {
    outDir: resolve(__dirname, 'resources/dist'),
    emptyOutDir: true,
    lib: {
      entry: resolve(__dirname, 'resources/js/init.ts'),
      formats: ['es'],
      fileName: () => 'init.js',
    },
  },
  plugins: [
    vueGlobalPlugin(),
    vue({
      template: {
        transformAssetUrls: {
          base: null,
          includeAbsolute: false,
        },
      },
    }),
  ],
  resolve: {
    alias: {
      '@': resolve(__dirname, 'resources/js'),
    },
  },
})
