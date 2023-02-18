<script setup>
import { Head } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    title: String,
    isGuest: Boolean
});
</script>
<template>
    <Head :title="title" />
    <AppLayout :isGuest="isGuest">
        <h1>{{ title }}</h1>
    </Applayout>
</template>
