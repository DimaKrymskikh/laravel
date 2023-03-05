<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';

defineProps({
    errors: Object | null
});

const form = useForm({
    login: null,
    password: null
});

const titlePage = 'Вход';
</script>

<template>
    <Head :title="titlePage" />
    <GuestLayout :errors="errors">
        <h1>{{ titlePage }}</h1>

        <form @submit.prevent="form.post('/login')">
            <div class="mb-3 w-1/3 pr-4">
                <label for="login" class="block font-medium text-sm text-gray-700">
                    Логин:
                </label>
                <input
                    id="login" type="text" name="login" 
                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                    v-model="form.login"
                >
                <div v-if="form.errors.login" class="error">{{ form.errors.login }}</div>
            </div>

            <div class="mb-3 w-1/3 pr-4">
                <label for="password" class="block font-medium text-sm text-gray-700">
                    Пароль:
                </label>
                <input
                    id="password" type="password" name="password"
                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                    v-model="form.password"
                >
                <div v-if="form.errors.password" class="error">{{ form.errors.password }}</div>
            </div>

            <div class="mb-3 w-1/3 pr-4 text-right">
                <button class="p-1 w-36 rounded-lg bg-orange-300 text-orange-700 hover:bg-orange-200 text-orange-600" :disabled="form.processing">
                    Вход
                </button>
            </div>
        </form>

        <div class="mb-4">
            Не зарегистрированы?
            <Link class="ml-2 text-orange-700 hover:text-orange-900" href="/register">Регистрация</Link>
        </div>
    </GuestLayout>
</template>
