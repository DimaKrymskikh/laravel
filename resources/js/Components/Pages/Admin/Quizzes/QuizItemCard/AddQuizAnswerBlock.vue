<script setup>
import { currentQuizItem, newQuizAnswer } from '@/Services/Content/Quizzes/quizItemCard';
import LockedButton from '@/Components/Buttons/Variants/LockedButton.vue';
import PrimaryButton from '@/Components/Buttons/Variants/PrimaryButton.vue';
import AddQuizAnswerModal from '@/Components/Pages/Admin/Quizzes/QuizItemCard/AddQuizAnswerModal.vue';

const props = defineProps({
    quizItem: Object
});

const buttonText = "Добавить ответ";

const showModal = function() {
    newQuizAnswer.show();
};
</script>

<template>
    <PrimaryButton
        v-if="currentQuizItem.isEditable"
        :buttonText="buttonText"
        :handler="showModal"
    />
    <LockedButton
        v-else
        :buttonText="buttonText"
    />
    
    <AddQuizAnswerModal
        v-if="newQuizAnswer.isShow"
        :quizItem="quizItem"
    />
</template>
