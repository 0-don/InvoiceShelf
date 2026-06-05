/**
 * Vue runtime shim for InvoiceShelf modules.
 *
 * The host app exposes its Vue instance on window.__invoiceshelf_vue.
 * This shim provides a Proxy-based default export that lazily resolves
 * Vue APIs on first access — avoiding the crash that happens when the
 * module script evaluates before the host has set the global.
 *
 * For named exports (used by SFC compiled templates), we use a Proxy
 * as the module namespace. Vite's lib mode with a default export
 * from a Proxy works because the compiled SFC template accesses
 * the APIs at render time (long after the host has initialized),
 * not at module evaluation time.
 */

function getVue() {
  const vue = window.__invoiceshelf_vue
  if (!vue) {
    throw new Error(
      '[InvoiceShelf Module] Host Vue runtime not available. ' +
      'Ensure the module script loads after the host app.'
    )
  }
  return vue
}

// Proxy that forwards all property access to the host's Vue at call time
const vueProxy = new Proxy({}, {
  get(_, key) {
    return getVue()[key]
  },
  has(_, key) {
    return key in getVue()
  },
})

export default vueProxy
