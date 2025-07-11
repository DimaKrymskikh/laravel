<script setup>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import BreadCrumb from '@/Components/Elements/BreadCrumb.vue';
import FormButton from '@/Components/Elements/FormButton.vue';
import InputField from '@/components/Elements/InputField.vue';
import { app } from '@/Services/app';

const props = defineProps({
    email: String,
    token: String,
    status: String | null,
    errors: Object
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: ''
});

const titlePage = 'Сброс пароля';

// Список для хлебных крошек
const linksList = [{
            link: '/guest',
            text: 'Главная страница'
        }, {
            link: '/login',
            text: 'Вход'
        }, {
            text: 'Сброс пароля'
        }];

const onBeforeForHandlerResetPassword = () => { app.isRequest = true; };

const onErrorForHandlerResetPassword = errors => { app.errorRequest(errors); };

const onFinishForHandlerResetPassword = () => {
            form.reset('password', 'password_confirmation');
            app.isRequest = false;
        };

const handlerResetPassword = () => {
    form.post('/reset-password', {
        onBefore: onBeforeForHandlerResetPassword,
        onError: onErrorForHandlerResetPassword,
        onFinish: onFinishForHandlerResetPassword
    });
};
</script>

<template>
    <Head :title="titlePage" />
    <GuestLayout>
        <BreadCrumb :linksList="linksList" />
        <h1>{{ titlePage }}</h1>

        <div id="reset-password-status" v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="handlerResetPassword" autocomplete="off">
            <div class="w-1/3">
                <InputField
                    titleText="Электронная почта:"
                    type="text"
                    :isInputDisabled="true"
                    :errorsMessage="form.errors.email"
                    v-model="form.email"
                />
            </div>

            <div class="w-1/3">
                <InputField
                    titleText="Введите пароль:"
                    type="password"
                    :isInputAutofocus="true"
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

            <div class="w-1/3 text-right">
                <FormButton class="w-48" text="Задать новый пароль" />
            </div>
        </form>
    </GuestLayout>
</template>
