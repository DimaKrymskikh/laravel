<script setup>
import { Head } from '@inertiajs/vue3'
import AuthLayout from '@/Layouts/AuthLayout.vue';
import BreadCrumb from '@/Components/Elements/BreadCrumb.vue';

defineProps({
    user: Object,
    errors: Object | null
});

const titlePage = 'Главная страница';

// Список для хлебных крошек
const linksList = [{
            text: 'Главная страница'
        }];
</script>

<template>
    <Head :title="titlePage" />
    <AuthLayout :errors="errors" :user="user">
        <BreadCrumb :linksList="linksList" />
        <h1>{{ titlePage }}</h1>
    </AuthLayout>
</template>
