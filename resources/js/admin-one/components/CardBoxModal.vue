<script setup>
import { computed, nextTick, onMounted, onUnmounted, ref, watch } from 'vue'
import BaseButton from '@/admin-one/components/BaseButton.vue'
import BaseButtons from '@/admin-one/components/BaseButtons.vue'
import CardBox from '@/admin-one/components/CardBox.vue'
import OverlayLayer from '@/admin-one/components/OverlayLayer.vue'
import CardBoxComponentFooter from '@/admin-one/components/CardBoxComponentFooter.vue'
import CardBoxModalHeader from '@/admin-one/components/CardBoxModalHeader.vue'
import CardBoxModalBody from '@/admin-one/components/CardBoxModalBody.vue'

const props = defineProps({
  title: {
    type: String,
    required: true,
  },
  button: {
    type: String,
    default: 'info',
  },
  buttonLabel: {
    type: String,
    default: 'Done',
  },
  hasCustomLayout: Boolean,
  hasCancel: Boolean,
  cancelLabel: {
    type: String,
    default: 'Cancel',
  },
  isForm: Boolean,
  isProcessing: Boolean,
  modelValue: [String, Number, Boolean],
})

const emit = defineEmits(['update:modelValue', 'cancel', 'confirm'])
const dialogRef = ref(null)
const lastFocusedElement = ref(null)

const value = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value),
})

const confirmCancel = (mode) => {
  if (mode === 'cancel' || (mode === 'confirm' && !props.isForm)) {
    value.value = false
  }

  emit(mode)
}

const confirm = () => confirmCancel('confirm')

const cancel = () => confirmCancel('cancel')

const getFocusableElements = () => {
  if (!(dialogRef.value instanceof HTMLElement)) {
    return []
  }

  return Array.from(
    dialogRef.value.querySelectorAll(
      'a[href], button:not([disabled]), textarea, input, select, [tabindex]:not([tabindex="-1"])',
    ),
  ).filter((element) => element instanceof HTMLElement && !element.hasAttribute('disabled'))
}

const focusFirstElement = async () => {
  await nextTick()
  const focusableElements = getFocusableElements()

  if (focusableElements.length > 0) {
    focusableElements[0].focus()
    return
  }

  if (dialogRef.value instanceof HTMLElement) {
    dialogRef.value.focus()
  }
}

const closeWithKeyboardEvent = (e) => {
  if (!value.value) {
    return
  }

  if (e.key === 'Escape') {
    cancel()
    return
  }

  if (e.key !== 'Tab') {
    return
  }

  const focusableElements = getFocusableElements()
  if (focusableElements.length === 0) {
    e.preventDefault()
    return
  }

  const firstElement = focusableElements[0]
  const lastElement = focusableElements[focusableElements.length - 1]
  const activeElement = document.activeElement

  if (e.shiftKey && activeElement === firstElement) {
    e.preventDefault()
    lastElement.focus()
    return
  }

  if (!e.shiftKey && activeElement === lastElement) {
    e.preventDefault()
    firstElement.focus()
  }
}

watch(
  value,
  async (isOpen) => {
    if (isOpen) {
      if (document.activeElement instanceof HTMLElement) {
        lastFocusedElement.value = document.activeElement
      } else {
        lastFocusedElement.value = null
      }

      await focusFirstElement()
      return
    }

    if (lastFocusedElement.value instanceof HTMLElement) {
      lastFocusedElement.value.focus()
    }
  },
)

onMounted(() => {
  window.addEventListener('keydown', closeWithKeyboardEvent)
})

onUnmounted(() => {
  window.removeEventListener('keydown', closeWithKeyboardEvent)
})
</script>

<template>
  <OverlayLayer v-if="value" @overlay-click="cancel">
    <CardBox
      ref="dialogRef"
      role="dialog"
      aria-modal="true"
      :aria-label="title"
      tabindex="-1"
      class="z-50 max-h-[calc(100dvh-(--spacing(40)))] w-11/12 animate-fade-in shadow-lg md:w-3/5 lg:w-2/5 xl:w-4/12"
      is-modal
      has-component-layout
      :is-form="isForm"
      @submit.prevent="confirm"
    >
      <CardBoxModalHeader :title="title" :has-cancel="hasCancel" @cancel="cancel" />

      <slot v-if="hasCustomLayout" />

      <template v-else>
        <CardBoxModalBody>
          <slot />
        </CardBoxModalBody>

        <CardBoxComponentFooter>
          <BaseButtons>
            <BaseButton
              :label="buttonLabel"
              :color="button"
              @click="isForm ? null : confirm()"
              :type="isForm ? 'submit' : 'button'"
              :disabled="isProcessing"
              :class="{ 'opacity-25': isProcessing }"
            />
            <BaseButton v-if="hasCancel" :label="cancelLabel" :color="button" outline @click="cancel" />
          </BaseButtons>
        </CardBoxComponentFooter>
      </template>
    </CardBox>
  </OverlayLayer>
</template>
