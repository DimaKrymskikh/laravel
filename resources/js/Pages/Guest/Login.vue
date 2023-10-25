<script setup>
import { inject, ref } from 'vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import BreadCrumb from '@/Components/Elements/BreadCrumb.vue';
import FormButton from '@/Components/Elements/FormButton.vue';
import InputField from '@/components/Elements/InputField.vue';

defineProps({
    errors: Object,
    status: String | null
});

const app = inject('app');

const form = useForm({
    login: null,
    password: null
});

const titlePage = 'Вход';

// Список для хлебных крошек
const linksList = [{
            link: '/guest',
            text: 'Главная страница'
        }, {
            text: 'Вход'
        }];
    
const handlerLogin = function() {
    form.post('/login', {
        onBefore: () => {
            app.isRequest = true;
        },
        onFinish: () => {
            app.isRequest = false;
            form.reset('password');
        }
    });
};
</script>

<template>
    <Head :title="titlePage" />
    <GuestLayout>
        <BreadCrumb :linksList="linksList" />
        <h1>{{ titlePage }}</h1>

        <div id="login-status" v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="handlerLogin" autocomplete="off">
            <div class="w-1/3">
                <InputField
                    titleText="Введите логин:"
                    type="text"
                    :isInputAutofocus="true"
                    :errorsMessage="form.errors.login"
                    v-model="form.login"
                />
            </div>

            <div class="w-1/3">
                <InputField
                    titleText="Введите пароль:"
                    type="password"
                    :errorsMessage="form.errors.password"
                    v-model="form.password"
                />
            </div>

            <div class="w-1/3 text-right">
                <FormButton class="w-36" text="Вход" />
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
