<script setup>
import { Head } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue';
import BreadCrumb from '@/Components/Elements/BreadCrumb.vue';

defineProps({
    errors: Object
});

const titlePage = 'Страница админа';

// Список для хлебных крошек
const linksList = [{
            text: 'Страница админа'
        }];
</script>

<template>
    <Head :title="titlePage" />
    <AdminLayout :errors="errors">
        <BreadCrumb :linksList="linksList" />
        <h1>{{ titlePage }}</h1>
    </AdminLayout>
</template>
