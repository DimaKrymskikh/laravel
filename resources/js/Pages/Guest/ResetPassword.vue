<script setup>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import BreadCrumb from '@/Components/Elements/BreadCrumb.vue';
import FormButton from '@/Components/Elements/FormButton.vue';

const props = defineProps({
    email: String,
    token: String,
    errors: Object | null
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: ''
});

const titlePage = 'Сброс пароля';

// Выполняется ли запрос на сервер
const isRequest = ref(false);

// Список для хлебных крошек
const linksList = [{
            link: '/',
            text: 'Главная страница'
        }, {
            link: '/login',
            text: 'Вход'
        }, {
            text: 'Сброс пароля'
        }];

const handlerResetPassword = () => {
    form.post('/reset-password', {
        onBefore: () => isRequest.value = true,
        onFinish: () => {
            form.reset('password', 'password_confirmation');
            isRequest.value = false;
        }
    });
};
</script>

<template>
    <Head :title="titlePage" />
    <GuestLayout :errors="errors">
        <BreadCrumb :linksList="linksList" />
        <h1>{{ titlePage }}</h1>

        <form @submit.prevent="handlerResetPassword">
            <div class="mb-3 w-1/3 pr-4">
                <label for="email" class="block font-medium text-sm text-gray-700">
                    Электронная почта:
                </label>
                <input
                    id="email" type="email" name="email" 
                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                >
                <div v-if="form.errors.email" class="error">{{ form.errors.email }}</div>
            </div>

            <div class="mb-3 w-1/3 pr-4">
                <label for="password" class="block font-medium text-sm text-gray-700">
                    Пароль:
                </label>
                <input
                    id="password" type="password" name="password"
                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                    v-model="form.password"
                    required
                    autocomplete="new-password"
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
                    required
                    autocomplete="new-password"
                >
                <div v-if="form.errors.password" class="error">{{ form.errors.verification }}</div>
            </div>

            <div class="mb-3 w-1/3 pr-4 text-right">
                <FormButton class="w-48" text="Задать новый пароль" :form="form" :isRequest="isRequest" />
            </div>
        </form>
    </GuestLayout>
</template>
