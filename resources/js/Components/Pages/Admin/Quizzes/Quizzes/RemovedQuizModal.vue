<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { app } from '@/Services/app';
import { removedQuiz } from '@/Services/Content/Quizzes/quizzes';
import { defaultOnBefore, defaultOnError, defaultOnFinish } from '@/Services/router';
import Spinner from '@/components/Svg/Spinner.vue';
import BaseModal from '@/Components/Modal/Request/BaseModal.vue';

const hideModal = function() {
    removedQuiz.hide();
};

const url = removedQuiz.isRemoved ? 'cancel_status' : 'set_status';

const title = removedQuiz.isRemoved ? "Отмена статуса 'удалён'" : "Задание статуса 'удалён'";
const question = removedQuiz.isRemoved ? "Вы хотите отменить у опроса статус 'удалён'?"
                    : "Вы хотите перевести опрос в статус 'удалён'?";
const data = removedQuiz.isRemoved ? {} : {status: 'removed'};

const onSuccess = () => { removedQuiz.hide(); };

const handlerRemovedQuiz = function(e) {
    // Защита от повторного запроса
    if(e.currentTarget.classList.contains('disabled')) {
        return;
    }
    
    router.put(`/admin/quizzes/${removedQuiz.id}/${url}`, data, {
        onBefore: defaultOnBefore,
        onSuccess,
        onError: defaultOnError(hideModal),
        onFinish: defaultOnFinish
    });
};

</script>

<template>
    <BaseModal
        :headerTitle="title"
        :hideModal="hideModal"
        :handlerSubmit="handlerRemovedQuiz"
    >
        <template v-slot:body>
            <div class="mb-3">
                <span class="text-orange-800">Опрос: </span> {{ removedQuiz.title }}
            </div>
            <div class="mb-3 text-orange-600">
                {{ question }}
            </div>
            <div v-if="app.isRequest">
                <Spinner class="spinner" styleSpinner="h-6 fill-gray-700 text-gray-200" />
            </div>
        </template>
    </BaseModal>
</template>
