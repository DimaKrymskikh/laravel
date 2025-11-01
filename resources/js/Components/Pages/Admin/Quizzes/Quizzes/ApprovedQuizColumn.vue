<script setup>
import { approvedQuiz } from '@/Services/Content/Quizzes/quizzes';
import AppIndicatorSvg from '@/Components/Svg/AppIndicatorSvg.vue';

const props = defineProps({
    quiz: Object
});

const isApproved = (quiz) => quiz.status.name === 'утверждён';
const isShow = (quiz) => quiz.status.name === 'утверждён' || quiz.status.name === 'готов';

const showModal = function(quiz) {
    if(!isShow(quiz)) {
        return;
    }
    approvedQuiz.isApproved = isApproved(quiz);
    approvedQuiz.id = quiz.id;
    approvedQuiz.title = quiz.title;
    approvedQuiz.show();
};
</script>

<template>
    <td>
        <AppIndicatorSvg :title="quiz.status.titleSvg" :color="quiz.status.colorSvg" @click="showModal(quiz)" />
    </td>
</template>
