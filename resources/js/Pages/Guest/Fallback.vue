<script setup>
import { Head } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';

defineProps({
    errors: Object
});

const titlePage = 'Страница не найдена';
</script>

<template>
    <Head :title="titlePage" />
    <GuestLayout>
        <h1>{{ titlePage }}</h1>
    </GuestLayout>
</template>
