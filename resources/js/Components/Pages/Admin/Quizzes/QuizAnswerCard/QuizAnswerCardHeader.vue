<script setup>
const props = defineProps({
    quizAnswer: Object
});
</script>

<template>
    <div class="mb-4">
        <span class="text-orange-800">Опрос: </span>
        <span>{{ quizAnswer.quiz_item.quiz.title }}</span>
    </div>

    <div class="mb-4">
        <span class="text-orange-800">Статус опроса: </span>
        <span :class="quizAnswer.quiz_item.quiz.status.style">{{ quizAnswer.quiz_item.quiz.status.name }}</span>
    </div>

    <div class="mb-4">
        <span class="text-orange-800">Вопрос: </span>
        <span>{{ quizAnswer.quiz_item.description }}</span>
    </div>

    <div class="mb-4">
        <span class="text-orange-800">Статус вопроса: </span>
        <span :class="quizAnswer.quiz_item.status.style">{{ quizAnswer.quiz_item.status.name }}</span>
    </div>
</template>
