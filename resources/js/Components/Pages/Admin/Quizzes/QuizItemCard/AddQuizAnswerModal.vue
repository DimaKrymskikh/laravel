<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { newQuizAnswer } from '@/Services/Content/Quizzes/quizItemCard';
import { defaultOnBefore, defaultOnError, defaultOnFinish } from '@/Services/router';
import Textarea from '@/Components/Elements/Form/Textarea/Textarea.vue';
import BaseModal from '@/Components/Modal/Request/BaseModal.vue';
import Checkbox from '@/components/Elements/Form/Checkbox.vue';

const { quizItem } = defineProps({
    quizItem: Object
});

const quizAnswerDescription = ref(newQuizAnswer.description);
const quizAnswerIsCorrect = ref(newQuizAnswer.isCorrect);

const hideModal = function() {
    newQuizAnswer.hide();
};

const onSuccess = () => { newQuizAnswer.hide(); };

const handlerAddQuizAnswer = function(e) {
    // Защита от повторного запроса
    if(e.currentTarget.classList.contains('disabled')) {
        return;
    }
    
    router.post('/admin/quiz_answers', {
            quiz_item_id: quizItem.id,
            description: quizAnswerDescription.value,
            is_correct: quizAnswerIsCorrect.value
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
        headerTitle="Добавление ответа"
        :hideModal="hideModal"
        :handlerSubmit="handlerAddQuizAnswer"
    >
        <template v-slot:body>
            <div class="mb-3">
                <Textarea
                    titleText="Текст ответа:"
                    v-model="quizAnswerDescription"
                    :isInputAutofocus="true"
                />
            </div>
            
            <div>
                <Checkbox 
                    titleText="Ответ правильный:"
                    v-model="quizAnswerIsCorrect"
                />
            </div>
        </template>
    </BaseModal>
</template>
