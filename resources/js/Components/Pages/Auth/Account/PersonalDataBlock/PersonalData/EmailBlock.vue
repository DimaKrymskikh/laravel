<script setup>
import { inject } from 'vue';
import { useForm } from '@inertiajs/vue3';
import CheckSvg from '@/Components/Svg/CheckSvg.vue';
import FormButton from '@/Components/Elements/FormButton.vue';
    
defineProps({
    user: Object | null
});

const app = inject('app');
const form = useForm({});

const onBeforeForHandlerVerifyEmail = () => { app.isRequest = true; };

const onFinishForHandlerVerifyEmail = () => { app.isRequest = false; };
    
const handlerVerifyEmail = function() {
    form.post('/verify-email', {
        onBefore: onBeforeForHandlerVerifyEmail,
        onFinish: onFinishForHandlerVerifyEmail
    });
};

</script>

<template>
     <div class="text-orange-900">
        Эл. почта:
    </div>
    <div class="flex justify-between" :class="user.email_verified_at ? 'mb-4' : 'mb-2'">
        <span>{{ user.email }}</span>
        <CheckSvg v-if="user.email_verified_at" />
    </div>
    <div class="mb-4" v-if="!user.email_verified_at">
        <div class="text-sm text-justify text-red-700">
            Ваша эл. почта не подтверждена.
            В почтовом ящике должно быть письмо со ссылкой для подтверждения.
            Вы можете нажать на эту кнопку для отправки нового письма
        </div>
        <div class="text-center">
            <form @submit.prevent="handlerVerifyEmail">
                <FormButton class="w-56" text="Отправка нового письма" />
            </form>
        </div>
    </div>
</template>
