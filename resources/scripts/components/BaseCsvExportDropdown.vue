<template>
  <BaseDropdown v-if="show" width-class="w-64">
    <template #activator>
      <BaseButton variant="primary-outline" :disabled="loading">
        <template #left="slotProps">
          <BaseIcon name="ArrowDownTrayIcon" :class="slotProps.class" />
        </template>
        {{ $t('general.export') }}
        <template #right="slotProps">
          <BaseIcon name="ChevronDownIcon" :class="slotProps.class" />
        </template>
      </BaseButton>
    </template>

    <BaseDropdownItem @click.prevent="exportDocument">
      <BaseIcon
        name="DocumentTextIcon"
        class="w-5 h-5 mr-3 text-gray-400 group-hover:text-gray-500"
      />
      {{ documentOptionLabel }}
    </BaseDropdownItem>

    <BaseDropdownItem @click.prevent="exportLines">
      <BaseIcon
        name="QueueListIcon"
        class="w-5 h-5 mr-3 text-gray-400 group-hover:text-gray-500"
      />
      {{ linesOptionLabel }}
    </BaseDropdownItem>
  </BaseDropdown>
</template>

<script setup>
import { computed, ref } from 'vue'
import { useI18n } from 'vue-i18n'
import { downloadCsvExport } from '@/scripts/helpers/csv-export'

const props = defineProps({
  url: {
    type: String,
    required: true,
  },
  params: {
    type: Object,
    default: () => ({}),
  },
  show: {
    type: Boolean,
    default: true,
  },
  documentType: {
    type: String,
    required: true,
    validator: (value) => ['invoice', 'estimate'].includes(value),
  },
})

const { t } = useI18n()
const loading = ref(false)

const documentOptionLabel = computed(() =>
  props.documentType === 'estimate'
    ? t('general.export_one_line_per_estimate')
    : t('general.export_one_line_per_invoice'),
)

const linesOptionLabel = computed(() =>
  props.documentType === 'estimate'
    ? t('general.export_one_line_per_estimate_item')
    : t('general.export_one_line_per_invoice_item'),
)

async function exportDocument() {
  await runExport(props.params)
}

async function exportLines() {
  await runExport({ ...props.params, format: 'lines' })
}

async function runExport(params) {
  loading.value = true

  try {
    await downloadCsvExport(props.url, params)
  } finally {
    loading.value = false
  }
}
</script>
