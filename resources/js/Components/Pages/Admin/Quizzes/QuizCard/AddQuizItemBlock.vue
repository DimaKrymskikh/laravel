<script setup>
import { currentQuiz, newQuizItem } from '@/Services/Content/Quizzes/quizCard';
import LockedButton from '@/Components/Buttons/Variants/LockedButton.vue';
import PrimaryButton from '@/Components/Buttons/Variants/PrimaryButton.vue';
import AddQuizItemModal from '@/Components/Pages/Admin/Quizzes/QuizCard/AddQuizItemModal.vue';

const { quiz } = defineProps({
    quiz: Object
});

const buttonText = "Добавить вопрос";

const showModal = function() {
    newQuizItem.show();
};
</script>

<template>
    <PrimaryButton
        v-if="currentQuiz.isEditable"
        :buttonText="buttonText"
        :handler="showModal"
    />
    <LockedButton
        v-else
        :buttonText="buttonText"
    />
    
    <AddQuizItemModal
        v-if="newQuizItem.isShow"
        :quiz="quiz"
    />
</template>
