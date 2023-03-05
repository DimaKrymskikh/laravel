<script setup>
import { Head } from '@inertiajs/vue3'
import GuestLayout from '@/Layouts/GuestLayout.vue';

defineProps({
    errors: Object | null
});

const titlePage = 'Главная страница';
</script>

<template>
    <Head :title="titlePage" />
    <GuestLayout :errors="errors">
        <h1>{{ titlePage }}</h1>
    </GuestLayout>
</template>
