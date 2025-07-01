<script setup>
import { inject, ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import BreadCrumb from '@/Components/Elements/BreadCrumb.vue';
import FormButton from '@/Components/Elements/FormButton.vue';
import Checkbox from '@/components/Elements/Form/Checkbox.vue';
import InputField from '@/components/Elements/InputField.vue';
import { app } from '@/Services/app';

defineProps({
    errors: Object
});

const form = useForm({
    login: '',
    email: '',
    password: '',
    password_confirmation: '',
    is_remember: false
});

const titlePage = 'Регистрация';

// Список для хлебных крошек
const linksList = [{
            link: '/guest',
            text: 'Главная страница'
        }, {
            link: '/login',
            text: 'Вход'
        }, {
            text: 'Регистрация'
        }];

const onBeforeForHandlerRegister = () => { app.isRequest = true; };

const onFinishForHandlerRegister = () => {
            app.isRequest = false;
            form.reset('password', 'password_confirmation');
        };
    
const handlerRegister = function() {
    form.post('/register', {
        onBefore: onBeforeForHandlerRegister,
        onFinish: onFinishForHandlerRegister
    });
};
</script>

<template>
    <Head :title="titlePage" />
    <GuestLayout>
        <BreadCrumb :linksList="linksList" />
        <h1>{{ titlePage }}</h1>

        <form @submit.prevent="handlerRegister" autocomplete="off">
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
                    titleText="Введите электронную почту:"
                    type="text"
                    :errorsMessage="form.errors.email"
                    v-model="form.email"
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

            <div class="w-1/3">
                <InputField
                    titleText="Подтверждение пароля:"
                    type="password"
                    v-model="form.password_confirmation"
                />
            </div>

            <div class="w-1/3">
                <Checkbox 
                    titleText="Запомнить меня:"
                    v-model="form.is_remember"
                />
            </div>

            <div class="w-1/3 text-right">
                <FormButton class="w-48" text="Зарегистрироваться" />
            </div>
        </form>
    </GuestLayout>
</template>
