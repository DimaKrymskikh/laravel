<script setup>
import { Head } from '@inertiajs/vue3'
import AuthLayout from '@/Layouts/AuthLayout.vue';

defineProps({
    errors: Object | null
});

const titlePage = 'Главная страница';
</script>

<template>
    <Head :title="titlePage" />
    <AuthLayout :errors="errors">
        <h1>{{ titlePage }}</h1>
    </AuthLayout>
</template>
