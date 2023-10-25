<script setup>
import { Head } from '@inertiajs/vue3'
import GuestLayout from '@/Layouts/GuestLayout.vue';
import BreadCrumb from '@/Components/Elements/BreadCrumb.vue';

defineProps({
    errors: Object
});

const titlePage = 'Главная страница';

// Список для хлебных крошек
const linksList = [{
            text: 'Главная страница'
        }];
</script>

<template>
    <Head :title="titlePage" />
    <GuestLayout>
        <BreadCrumb :linksList="linksList" />
        <h1>{{ titlePage }}</h1>
        <div>Главная страница для не аутентифицированного пользователя</div>
    </GuestLayout>
</template>
