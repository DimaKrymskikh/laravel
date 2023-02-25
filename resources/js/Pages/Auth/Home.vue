<script setup>
import { Head } from '@inertiajs/vue3'
import AuthLayout from '@/Layouts/AuthLayout.vue';

const titlePage = 'Главная страница';
</script>

<template>
    <Head :title="titlePage" />
    <AuthLayout>
        <h1>{{ titlePage }}</h1>
    </AuthLayout>
</template>
