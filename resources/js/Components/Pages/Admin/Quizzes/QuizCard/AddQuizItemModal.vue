<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { app } from '@/Services/app';
import { newQuizItem } from '@/Services/Content/Quizzes/quizCard';
import { defaultOnBefore, defaultOnError, defaultOnFinish } from '@/Services/router';
import Textarea from '@/Components/Elements/Form/Textarea/Textarea.vue';
import BaseModal from '@/Components/Modal/Request/BaseModal.vue';

const { quiz } = defineProps({
    quiz: Object
});

const quizItemDescription = ref(newQuizItem.description);

const hideModal = function() {
    newQuizItem.hide();
};

const onSuccess = () => { newQuizItem.hide(); };

const handlerAddQuizItem = function(e) {
    // Защита от повторного запроса
    if(e.currentTarget.classList.contains('disabled')) {
        return;
    }
    
    router.post('/admin/quiz_items', {
            quiz_id: quiz.id,
            description: quizItemDescription.value
        }, {
        onBefore: defaultOnBefore,
        onSuccess,
        onError: defaultOnError(hideModal),
        onFinish: defaultOnFinish
    });
};

</script>

<template>
    <BaseModal
        headerTitle="Добавление вопроса"
        :hideModal="hideModal"
        :handlerSubmit="handlerAddQuizItem"
    >
        <template v-slot:body>
            <div class="mb-3">
                <Textarea
                    titleText="Описание вопроса:"
                    v-model="quizItemDescription"
                    :isInputAutofocus="true"
                />
            </div>
        </template>
    </BaseModal>
</template>
