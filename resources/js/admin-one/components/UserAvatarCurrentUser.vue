<script setup>
import UserAvatar from '@/admin-one/components/UserAvatar.vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
</script>

<template>
  <UserAvatar :username="page.props.auth?.user?.name ?? 'User'">
    <slot />
  </UserAvatar>
</template>
