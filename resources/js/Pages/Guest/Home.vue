<script setup>
import { Head } from '@inertiajs/vue3'
import GuestLayout from '@/Layouts/GuestLayout.vue';
import BreadCrumb from '@/Components/Elements/BreadCrumb.vue';

defineProps({
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
    <GuestLayout :errors="errors">
        <BreadCrumb :linksList="linksList" />
        <h1>{{ titlePage }}</h1>
    </GuestLayout>
</template>
