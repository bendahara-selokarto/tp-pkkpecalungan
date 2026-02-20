<script setup>
import NotificationBar from '@/admin-one/components/NotificationBar.vue'
import { mdiAlertCircle, mdiCheckCircle, mdiInformation } from '@mdi/js'
import { computed } from 'vue'

const props = defineProps({
  flash: {
    type: Object,
    default: () => ({}),
  },
  showStatus: {
    type: Boolean,
    default: true,
  },
})

const messages = computed(() => {
  const flash = props.flash ?? {}
  const statusMessage = props.showStatus ? flash.status : null

  const items = [
    {
      key: 'success',
      text: flash.success,
      color: 'success',
      icon: mdiCheckCircle,
    },
    {
      key: 'error',
      text: flash.error,
      color: 'danger',
      icon: mdiAlertCircle,
    },
    {
      key: 'status',
      text: statusMessage,
      color: 'info',
      icon: mdiInformation,
    },
  ]

  return items.filter((item) => typeof item.text === 'string' && item.text.trim().length > 0)
})
</script>

<template>
  <NotificationBar
    v-for="message in messages"
    :key="message.key"
    :color="message.color"
    :icon="message.icon"
  >
    {{ message.text }}
  </NotificationBar>
</template>

