<template>
  <BaseButton
    v-if="show"
    variant="primary-outline"
    :disabled="loading"
    @click="onExport"
  >
    <template #left="slotProps">
      <BaseIcon name="ArrowDownTrayIcon" :class="slotProps.class" />
    </template>
    {{ label ?? $t('general.export_csv') }}
  </BaseButton>
</template>

<script setup>
import { ref } from 'vue'
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
  label: {
    type: String,
    default: null,
  },
})

const loading = ref(false)

async function onExport() {
  loading.value = true

  try {
    await downloadCsvExport(props.url, props.params)
  } finally {
    loading.value = false
  }
}
</script>
