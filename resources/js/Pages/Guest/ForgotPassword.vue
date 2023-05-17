<script setup>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import BreadCrumb from '@/Components/Elements/BreadCrumb.vue';
import FormButton from '@/Components/Elements/FormButton.vue';

defineProps({
    errors: Object | null,
    status: String
});

const form = useForm({
    email: null
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
    
const handlerForgotPassword = function() {
    form.post('/forgot-password', {
        onBefore: () => isRequest.value = true,
        onFinish: () => isRequest.value = false
    });
};
</script>

<template>
    <Head :title="titlePage" />
    <GuestLayout :errors="errors">
        <BreadCrumb :linksList="linksList" />
        <h1>{{ titlePage }}</h1>

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="handlerForgotPassword">
            <div class="mb-3 w-1/3 pr-4">
                <label for="email" class="block font-medium text-sm text-gray-700">
                    Электронная почта:
                </label>
                <input
                    id="email" type="email" name="email" 
                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                    v-model="form.email"
                >
                <div v-if="form.errors.email" class="error">{{ form.errors.email }}</div>
            </div>

            <div class="mb-3 w-1/3 pr-4 text-right">
                <FormButton class="w-96" text="Ссылка для сброса пароля электронной почты" :form="form" :isRequest="isRequest" />
            </div>
        </form>
    </GuestLayout>
</template>

