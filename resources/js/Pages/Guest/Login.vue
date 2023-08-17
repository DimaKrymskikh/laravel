<script setup>
import { ref } from 'vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import BreadCrumb from '@/Components/Elements/BreadCrumb.vue';
import FormButton from '@/Components/Elements/FormButton.vue';

defineProps({
    errors: Object | null,
    status: String | null
});

const form = useForm({
    login: null,
    password: null
});

const titlePage = 'Вход';

// Список для хлебных крошек
const linksList = [{
            link: '/',
            text: 'Главная страница'
        }, {
            text: 'Вход'
        }];

// Выполняется ли запрос на сервер
const isRequest = ref(false);
    
const handlerLogin = function() {
    form.post('/login', {
        onBefore: () => {
            isRequest.value = true;
        },
        onFinish: () => {
            isRequest.value = false;
            form.reset('password');
        }
    });
};
</script>

<template>
    <Head :title="titlePage" />
    <GuestLayout :errors="errors">
        <BreadCrumb :linksList="linksList" />
        <h1>{{ titlePage }}</h1>

        <div id="login-status" v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="handlerLogin">
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
                <FormButton class="w-36" text="Вход" :processing="form.processing" :isRequest="isRequest" />
            </div>
        </form>

        <div class="mb-4">
            Не зарегистрированы?
            <Link class="ml-2 text-orange-700 hover:text-orange-900" href="/register">Регистрация</Link>
        </div>

        <div class="mb-4">
            Забыли пароль?
            <Link class="ml-2 text-orange-700 hover:text-orange-900" href="/forgot-password">Сброс пароля</Link>
        </div>
    </GuestLayout>
</template>
