/**
 * HelloWorld module entry point.
 *
 * This file is loaded by the host app via a <script type="module"> tag
 * injected by Registry::allScripts(). It runs BEFORE the Vue app mounts,
 * so router.addRoute() works reliably.
 *
 * All Base* components (BaseCard, BaseIcon, BasePage, etc.) are globally
 * registered by the host — no imports needed in your Vue templates.
 */
import DashboardPage from './pages/DashboardPage.vue'

window.InvoiceShelf.booting((_app, router) => {
  // Register a Vue page route
  router.addRoute('admin', {
    path: 'modules/hello-world/dashboard',
    name: 'modules.hello-world.dashboard',
    component: DashboardPage,
    meta: {
      requiresAuth: true,
    },
  })
})
