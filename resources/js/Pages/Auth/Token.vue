<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { inject } from 'vue';
import AuthLayout from '@/Layouts/AuthLayout.vue';

defineProps({
    user: Object,
    errors: Object,
    token: String | null
});

const titlePage = 'Токен';

const filmsAccount = inject('filmsAccount');
</script>

<template>
    <Head :title="titlePage" />
    <AuthLayout :errors="errors" :user="user">
        <h1>{{ titlePage }}</h1>
        <div>
            Сохраните полученный токен:
        </div>
        <div class="font-sans my-2">
            {{ token }}
        </div>
        <Link id="account-link" class="ml-2 text-orange-700 hover:text-orange-900" :href="filmsAccount.getUrl('/userfilms')">
            Перейти в личный кабинет
        </Link>
    </AuthLayout>
</template>
