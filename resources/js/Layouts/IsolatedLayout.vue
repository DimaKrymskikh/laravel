<script setup>
import { app } from '@/Services/app';
import { useGlobalRequest } from '@/Services/inertia';
import ForbiddenModal from '@/components/Modal/ForbiddenModal.vue';
import GlobalModal from '@/components/Modal/GlobalModal.vue';

const isGlobalRequest = useGlobalRequest();
</script>

<template>
    <slot />
    
    <ForbiddenModal />
    <GlobalModal v-if="isGlobalRequest && !app.isRequest" />
</template>
