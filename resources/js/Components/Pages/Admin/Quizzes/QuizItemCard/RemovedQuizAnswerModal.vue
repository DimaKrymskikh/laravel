<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { app } from '@/Services/app';
import { removedQuizAnswer } from '@/Services/Content/Quizzes/quizItemCard';
import { defaultOnBefore, defaultOnError, defaultOnFinish } from '@/Services/router';
import Spinner from '@/components/Svg/Spinner.vue';
import BaseModal from '@/Components/Modal/Request/BaseModal.vue';

const { quizItem, quizAnswer } = defineProps({
    quizItem: Object
});

const hideModal = function() {
    removedQuizAnswer.hide();
};

const onSuccess = () => { removedQuizAnswer.hide(); };

const handlerRemoveQuizAnswer = function(e) {
    // Защита от повторного запроса
    if(e.currentTarget.classList.contains('disabled')) {
        return;
    }
    
    router.delete(`/admin/quiz_answers/${removedQuizAnswer.id}`, {
        onBefore: defaultOnBefore,
        onSuccess,
        onError: defaultOnError(hideModal),
        onFinish: defaultOnFinish
    });
};

</script>

<template>
    <BaseModal
        headerTitle="Удаление ответа"
        :hideModal="hideModal"
        :handlerSubmit="handlerRemoveQuizAnswer"
    >
        <template v-slot:body>
            <div class="mb-3">
                <span class="text-orange-800">Вопрос: </span> {{ quizItem.description }}
            </div>
            <div class="mb-3">
                <span class="text-orange-800">Ответ: </span> {{ removedQuizAnswer.description }}
            </div>
            <div class="mb-3 text-orange-600">
                Вы хотитет удалить ответ?
            </div>
            <div v-if="app.isRequest">
                <Spinner class="spinner" styleSpinner="h-6 fill-gray-700 text-gray-200" />
            </div>
        </template>
    </BaseModal>
</template>
