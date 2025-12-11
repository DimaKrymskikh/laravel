<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { trialQuestions } from '@/Services/Content/Trials/trials';
import { defaultOnBefore, defaultOnFinish, defaultOnError } from '@/Services/router';
import BaseModal from '@/Components/Modal/Request/BaseModal.vue';
import RadioInput from '@/Components/Elements/Form/Input/RadioInput.vue';
import RadioGroup from '@/Components/Elements/Groups/RadioGroup.vue';

const props = defineProps({
    quizItems: Object
});

const chosenQuestion = props.quizItems.find(item => item.id === trialQuestions.activeQuestion.quiz_item_id);

const hideModal = function() {
    trialQuestions.hide();
};

const chosenAnswer = ref(null);

const onFinish = () => {
    defaultOnFinish();
    trialQuestions.hide();
};

const handlerChooseAnswer = function(e) {
    // Если ответ не выбран
    if(!chosenAnswer.value) {
        return;
    }
    
    router.post('/trials/choose_answer', {
            id: trialQuestions.activeQuestion.id,
            quiz_answer_id: chosenAnswer.value
        }, {
        onBefore: defaultOnBefore,
        onError: defaultOnError(trialQuestions.hide.bind(trialQuestions)),
        onFinish
    });
};

</script>

<template>
    <BaseModal
        :headerTitle="trialQuestions.activeQuestion.question"
        :hideModal="hideModal"
        :handlerSubmit="handlerChooseAnswer"
    >
        <template v-slot:body>
            <div class="mb-3">
                <RadioGroup>
                    <template v-for="(answer) in chosenQuestion.answers" :key="answer.id">
                        <RadioInput 
                            :titleText="answer.description" 
                            name="trial" 
                            :radioValue="answer.id" 
                            :isChecked="trialQuestions.activeQuestion.quiz_answer_id === answer.id" 
                            v-model="chosenAnswer"/>
                    </template>
                </RadioGroup>
            </div>
        </template>
    </BaseModal>
</template>
