<script setup>
import CardBoxModal from '@/admin-one/components/CardBoxModal.vue'
import { computed } from 'vue'

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false,
  },
  title: {
    type: String,
    default: 'Konfirmasi Tindakan',
  },
  message: {
    type: String,
    default: 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
  },
  confirmLabel: {
    type: String,
    default: 'Ya, Lanjutkan',
  },
  cancelLabel: {
    type: String,
    default: 'Batal',
  },
  confirmButton: {
    type: String,
    default: 'danger',
  },
  isProcessing: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['update:modelValue', 'confirm', 'cancel'])

const value = computed({
  get: () => props.modelValue,
  set: (nextValue) => emit('update:modelValue', nextValue),
})

const onConfirm = () => emit('confirm')

const onCancel = () => emit('cancel')
</script>

<template>
  <CardBoxModal
    v-model="value"
    :title="title"
    :button="confirmButton"
    :button-label="confirmLabel"
    :cancel-label="cancelLabel"
    :is-processing="isProcessing"
    has-cancel
    @confirm="onConfirm"
    @cancel="onCancel"
  >
    <p class="text-sm text-gray-700 dark:text-gray-200">
      {{ message }}
    </p>
  </CardBoxModal>
</template>
