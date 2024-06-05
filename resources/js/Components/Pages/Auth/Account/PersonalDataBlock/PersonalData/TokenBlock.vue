<script setup>
import { inject } from 'vue';
import { useForm } from '@inertiajs/vue3';
import FormButton from '@/Components/Elements/FormButton.vue';

const app = inject('app');
const form = useForm({});

const onBeforeForHandlerGettingToken = () => { app.isRequest = true; };

const onFinishForHandlerGettingToken = () => { app.isRequest = false; };

const handlerGettingToken = function() {
    form.post('/token', {
        onBefore: onBeforeForHandlerGettingToken,
        onFinish: onFinishForHandlerGettingToken
    });
};

</script>

<template>
    <div class="text-orange-900">
        Токен:
    </div>
    <div class="text-sm text-justify mb-2">
        Получите токен для взаимодействия с приложением через API.
    </div>
    <div class="mb-4">
        <div class="text-center">
            <form @submit.prevent="handlerGettingToken">
                <FormButton class="w-56" text="Получить токен" />
            </form>
        </div>
    </div>
</template>
