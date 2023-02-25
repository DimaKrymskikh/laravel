<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    title: String,
    isGuest: Boolean
});

const form = useForm({
    login: null,
    password: null,
    password_confirmation: null
});
</script>

<template>
    <Head :title="title" />
    <AppLayout :isGuest="isGuest">
        <h1>{{ title }}</h1>

        <form @submit.prevent="form.post('/register')">
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

            <div class="mb-3 w-1/3 pr-4">
                <label for="password_confirmation" class="block font-medium text-sm text-gray-700">
                    Подтверждение пароля:
                </label>
                <input
                    id="password_confirmation" type="password" name="password_confirmation"
                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                    v-model="form.password_confirmation"
                >
                <div v-if="form.errors.password" class="error">{{ form.errors.verification }}</div>
            </div>

            <div class="mb-3 w-1/3 pr-4 text-right">
                <button class="p-1 w-48 rounded-lg bg-orange-300 text-orange-700 hover:bg-orange-200 text-orange-600" :disabled="form.processing">
                    Зарегистрироваться
                </button>
            </div>
        </form>
    </Applayout>
</template>
