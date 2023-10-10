<script setup>
import { inject, ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import BreadCrumb from '@/Components/Elements/BreadCrumb.vue';
import FormButton from '@/Components/Elements/FormButton.vue';
import InputField from '@/components/Elements/InputField.vue';

defineProps({
    status: String | null
});

const app = inject('app');

const form = useForm({
    email: null
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
    
const handlerForgotPassword = function() {
    form.post('/forgot-password', {
        onBefore: () => app.isRequest = true,
        onFinish: () => app.isRequest = false
    });
};
</script>

<template>
    <Head :title="titlePage" />
    <GuestLayout>
        <BreadCrumb :linksList="linksList" />
        <h1>{{ titlePage }}</h1>

        <div id="forgot-password-status" v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="handlerForgotPassword" autocomplete="off">
            <div class="w-1/3">
                <InputField
                    titleText="Введите электронную почту:"
                    type="text"
                    :isInputAutofocus="true"
                    :errorsMessage="form.errors.email"
                    v-model="form.email"
                />
            </div>

            <div class="w-1/3 text-right">
                <FormButton class="w-96" text="Ссылка для сброса пароля электронной почты" />
            </div>
        </form>
    </GuestLayout>
</template>
