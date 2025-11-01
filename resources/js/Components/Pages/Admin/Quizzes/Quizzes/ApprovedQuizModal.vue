<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { app } from '@/Services/app';
import { approvedQuiz } from '@/Services/Content/Quizzes/quizzes';
import { defaultOnBefore, defaultOnError, defaultOnFinish } from '@/Services/router';
import Spinner from '@/components/Svg/Spinner.vue';
import BaseModal from '@/Components/Modal/Request/BaseModal.vue';

const hideModal = function() {
    approvedQuiz.hide();
};

const url = approvedQuiz.isApproved ? 'cancel_status' : 'set_status';

const title = approvedQuiz.isApproved ? "Отмена статуса 'утверждён'" : "Задание статуса 'утверждён'";
const question = approvedQuiz.isApproved ? "Вы хотите отменить у опроса статус 'утверждён'?"
                    : "Вы хотите перевести опрос в статус 'утверждён'?";
const data = approvedQuiz.isApproved ? {} : {status: 'approved'};

const onSuccess = () => { approvedQuiz.hide(); };

const handlerApprovedQuiz = function(e) {
    // Защита от повторного запроса
    if(e.currentTarget.classList.contains('disabled')) {
        return;
    }
    
    router.put(`/admin/quizzes/${approvedQuiz.id}/${url}`, data, {
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
        :handlerSubmit="handlerApprovedQuiz"
    >
        <template v-slot:body>
            <div class="mb-3">
                <span class="text-orange-800">Опрос: </span> {{ approvedQuiz.title }}
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
