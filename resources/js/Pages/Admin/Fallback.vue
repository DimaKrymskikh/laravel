<script setup>
import { Head } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps({
    errors: Object
});

const titlePage = 'Страница не найдена';
</script>

<template>
    <Head :title="titlePage" />
    <AdminLayout :errors="errors">
        <h1>{{ titlePage }}</h1>
    </AdminLayout>
</template>
