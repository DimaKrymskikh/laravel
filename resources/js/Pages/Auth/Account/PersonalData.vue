<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import CheckSvg from '@/Components/Svg/CheckSvg.vue';
import FormButton from '@/Components/Elements/FormButton.vue';
    
defineProps({
    user: Object | null,
    token: String | null
});

const form = useForm({});

// Выполняется ли запрос на сервер
const isRequest = ref(false);
    
const handlerVerifyEmail = function() {
    form.post('/verify-email', {
        onBefore: () => isRequest.value = true,
        onFinish: () => isRequest.value = false
    });
};

const handlerGettingToken = function() {
    form.post('/account/getting-token', {
        onBefore: () => isRequest.value = true,
        onFinish: () => isRequest.value = false,
        only: ['token']
    });
};

</script>

<template>
    <div id="personal-data" class="w-80 h-screen bg-white p-4 shadow shadow-gray-500">
        <div id="pd-email">
            <div class="text-orange-900">
                Эл. почта:
            </div>
            <div class="flex justify-between mb-2">
                <span>{{ user.email }}</span>
                <CheckSvg v-if="user.email_verified_at" />
            </div>
            <div v-if="!user.email_verified_at">
                <div class="text-sm text-justify text-red-700">
                    Ваша эл. почта не подтверждена.
                    В почтовом ящике должно быть письмо со ссылкой для подтверждения.
                    Вы можете нажать на эту кнопку для отправки нового письма
                </div>
                <div class="text-center">
                    <form @submit.prevent="handlerVerifyEmail">
                        <FormButton class="w-56" text="Отправка нового письма" :processing="form.processing" :isRequest="isRequest" />
                    </form>
                </div>
            </div>
        </div>
        <div class="text-orange-900">
            Токен:
        </div>
        <div class="flex justify-between mb-2 overflow-x-scroll py-4">
            <span v-if="token">{{ token }}</span>
            <span v-else>Токен не получен</span>
        </div>
        <div>
            <div class="text-center">
                <form @submit.prevent="handlerGettingToken">
                    <FormButton class="w-56" :text="token ? 'Получить новый токен' : 'Получить токен'" :processing="form.processing" :isRequest="isRequest" />
                </form>
            </div>
        </div>
    </div>
</template>
