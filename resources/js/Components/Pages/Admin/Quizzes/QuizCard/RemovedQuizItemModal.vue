<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { app } from '@/Services/app';
import { removedQuizItem } from '@/Services/Content/Quizzes/quizCard';
import { defaultOnBefore, defaultOnError, defaultOnFinish } from '@/Services/router';
import Spinner from '@/components/Svg/Spinner.vue';
import Textarea from '@/Components/Elements/Form/Textarea/Textarea.vue';
import BaseModal from '@/Components/Modal/Request/BaseModal.vue';

const hideModal = function() {
    removedQuizItem.hide();
};

const url = removedQuizItem.isRemoved ? 'cancel_status' : 'set_status';

const title = removedQuizItem.isRemoved ? "Отмена статуса 'удалён'" : "Задание статуса 'удалён'";
const question = removedQuizItem.isRemoved ? "Вы хотите отменить у вопроса статус 'удалён'?"
                    : "Вы хотите перевести вопрос в статус 'удалён'?";
const data = removedQuizItem.isRemoved ? {} : {status: 'removed'};

const onSuccess = () => { removedQuizItem.hide(); };

const handlerRemovedQuizItem = function(e) {
    // Защита от повторного запроса
    if(e.currentTarget.classList.contains('disabled')) {
        return;
    }
    
    router.put(`/admin/quiz_items/${removedQuizItem.id}/${url}`, data, {
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
        :handlerSubmit="handlerRemovedQuizItem"
    >
        <template v-slot:body>
            <div class="mb-3">
                <span class="text-orange-800">Опрос: </span> {{ removedQuizItem.quizTitle }}
            </div>
            <div class="mb-3">
                <span class="text-orange-800">Вопрос: </span> {{ removedQuizItem.description }}
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
