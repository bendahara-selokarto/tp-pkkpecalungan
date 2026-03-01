<script setup>
defineProps({
  as: {
    type: String,
    default: 'button',
  },
  display: {
    type: String,
    default: 'flex',
  },
  useMargin: Boolean,
})
</script>

<template>
  <component
    :is="as"
    :type="as === 'button' ? 'button' : null"
    :class="[display, useMargin ? 'mx-3 my-2' : 'px-3 py-2']"
    class="navbar-item-label min-h-[44px] cursor-pointer items-center rounded-md dark:text-white dark:hover:text-slate-400"
  >
    <slot />
  </component>
</template>
