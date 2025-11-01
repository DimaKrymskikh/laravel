<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { app } from '@/Services/app';
import { newQuiz } from '@/Services/Content/Quizzes/quizzes';
import { defaultOnBefore, defaultOnError, defaultOnFinish } from '@/Services/router';
import InputField from '@/Components/Elements/InputField.vue';
import Textarea from '@/Components/Elements/Form/Textarea/Textarea.vue';
import BaseModal from '@/Components/Modal/Request/BaseModal.vue';

const quizTitle = ref(newQuiz.title);
const quizDescription = ref(newQuiz.description);
const quizLeadTime = ref(newQuiz.leadTime);

const errorsTitle = ref('');
const errorsLeadTime = ref('');

const hideModal = function() {
    newQuiz.hide();
};

const onBefore = () => {
            defaultOnBefore();
            errorsTitle.value = '';
            errorsLeadTime.value = '';
        };

const onSuccess = () => { newQuiz.hide(); };

const onError = errors => {
            errorsTitle.value = errors.title;
            errorsLeadTime.value = errors.lead_time;
            defaultOnError(hideModal);
        };

const handlerAddQuiz = function(e) {
    // Защита от повторного запроса
    if(e.currentTarget.classList.contains('disabled')) {
        return;
    }
    
    router.post('/admin/quizzes', {
            title: quizTitle.value,
            description: quizDescription.value,
            lead_time: quizLeadTime.value
        }, {
        onBefore,
        onSuccess,
        onError,
        onFinish: defaultOnFinish
    });
};

</script>

<template>
    <BaseModal
        headerTitle="Добавление опроса"
        :hideModal="hideModal"
        :handlerSubmit="handlerAddQuiz"
    >
        <template v-slot:body>
            <div class="mb-3">
                <InputField
                    titleText="Название опроса:"
                    type="text"
                    :errorsMessage="errorsTitle"
                    :isInputAutofocus="true"
                    v-model="quizTitle"
                />
                <Textarea
                    titleText="Описание опроса:"
                    v-model="quizDescription"
                />
                <InputField
                    titleText="Продолжительность опроса (в минутах):"
                    type="text"
                    :errorsMessage="errorsLeadTime"
                    v-model="quizLeadTime"
                />
            </div>
        </template>
    </BaseModal>
</template>
