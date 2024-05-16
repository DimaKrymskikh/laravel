<script setup>
import { Head } from '@inertiajs/vue3'
import AuthLayout from '@/Layouts/AuthLayout.vue';

defineProps({
    user: Object,
    errors: Object
});

const titlePage = 'Страница не найдена';
</script>

<template>
    <Head :title="titlePage" />
    <AuthLayout :errors="errors" :user="user">
        <h1>{{ titlePage }}</h1>
    </AuthLayout>
</template>
