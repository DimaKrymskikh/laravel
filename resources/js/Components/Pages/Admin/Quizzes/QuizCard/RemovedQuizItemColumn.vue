<script setup>
import { ref, onUpdated } from 'vue';
import { currentQuiz, removedQuizItem } from '@/Services/Content/Quizzes/quizCard';
import ReplySvg from '@/Components/Svg/ReplySvg.vue';
import TrashSvg from '@/Components/Svg/TrashSvg.vue';
import LockedReplySvg from '@/Components/Svg/Locked/LockedReplySvg.vue';
import LockedTrashSvg from '@/Components/Svg/Locked/LockedTrashSvg.vue';

const props = defineProps({
    quizItem: Object
});

const fnIsRemoved = (quizItem) => quizItem.status.name === 'удалён';
const isRemoved = ref(fnIsRemoved(props.quizItem));

const showModal = function(quizItem) {
    removedQuizItem.isRemoved = isRemoved.value;
    removedQuizItem.id = quizItem.id;
    removedQuizItem.description = quizItem.description;
    removedQuizItem.quizTitle = quizItem.quiz.title;
    removedQuizItem.show();
};

onUpdated(() => {
    isRemoved.value = fnIsRemoved(props.quizItem);
});
</script>

<template>
    <td v-if="currentQuiz.isEditable">
        <ReplySvg v-if="isRemoved" title="Отменить статус 'удалён' вопроса" @click="showModal(quizItem)" />
        <TrashSvg v-else title="Перевести вопрос в статус 'удалён'" @click="showModal(quizItem)" />
    </td>
    <td v-else>
        <LockedReplySvg v-if="isRemoved" title="Нельзя редактировать вопрос" />
        <LockedTrashSvg v-else title="Нельзя редактировать вопрос" />
    </td>
</template>
