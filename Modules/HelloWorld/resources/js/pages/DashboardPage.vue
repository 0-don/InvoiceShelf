<template>
  <BasePage>
    <BasePageHeader title="Hello World">
      <BaseBreadcrumb>
        <BaseBreadcrumbItem title="Home" to="dashboard" />
        <BaseBreadcrumbItem title="Hello World" to="#" active />
      </BaseBreadcrumb>
    </BasePageHeader>

    <BaseCard class="mt-6">
      <div class="p-6">
        <div class="flex items-center gap-3">
          <BaseIcon name="HandRaisedIcon" class="h-8 w-8 text-primary-500" />
          <h2 class="text-xl font-semibold text-heading">{{ greeting }}</h2>
        </div>
        <p class="mt-3 text-sm text-muted leading-relaxed">
          This page is provided by the <strong>HelloWorld</strong> module.
          It demonstrates how modules can ship their own Vue pages that
          render inside the InvoiceShelf SPA using globally registered
          Base components.
        </p>

        <div class="mt-6 rounded-lg bg-surface-secondary p-4">
          <h3 class="text-sm font-semibold text-heading mb-2">How it works</h3>
          <ul class="space-y-1.5 text-sm text-muted">
            <li class="flex items-start gap-2">
              <BaseIcon name="CheckIcon" class="h-4 w-4 text-green-500 shrink-0 mt-0.5" />
              Module ships a compiled <code class="text-primary-600">init.js</code> that calls <code class="text-primary-600">window.InvoiceShelf.booting()</code>
            </li>
            <li class="flex items-start gap-2">
              <BaseIcon name="CheckIcon" class="h-4 w-4 text-green-500 shrink-0 mt-0.5" />
              The callback receives <code class="text-primary-600">(app, router)</code> and adds routes via <code class="text-primary-600">router.addRoute()</code>
            </li>
            <li class="flex items-start gap-2">
              <BaseIcon name="CheckIcon" class="h-4 w-4 text-green-500 shrink-0 mt-0.5" />
              Vue pages use globally registered <code class="text-primary-600">Base*</code> components — no imports needed
            </li>
          </ul>
        </div>
      </div>
    </BaseCard>
  </BasePage>
</template>

<script setup>
import { ref } from 'vue'

const greeting = ref('Hello, world!')
</script>
