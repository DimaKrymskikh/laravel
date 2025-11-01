<script setup>
import { newQuiz } from '@/Services/Content/Quizzes/quizzes';
import PrimaryButton from '@/Components/Buttons/Variants/PrimaryButton.vue';
import AddQuizModal from '@/Components/Pages/Admin/Quizzes/Quizzes/AddQuizModal.vue';

const showModal = function() {
    newQuiz.show();
};
</script>

<template>
    <PrimaryButton
        buttonText="Добавить опрос"
        :handler="showModal"
    />
    <AddQuizModal
        v-if="newQuiz.isShow"
    />
</template>
