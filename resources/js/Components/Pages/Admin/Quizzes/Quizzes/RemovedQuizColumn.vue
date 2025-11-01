<script setup>
import { ref, onUpdated } from 'vue';
import { removedQuiz } from '@/Services/Content/Quizzes/quizzes';
import ReplySvg from '@/Components/Svg/ReplySvg.vue';
import TrashSvg from '@/Components/Svg/TrashSvg.vue';
import LockedTrashSvg from '@/Components/Svg/Locked/LockedTrashSvg.vue';

const props = defineProps({
    quiz: Object
});

const isEditable = ref(props.quiz.status.isEditable);

const fnIsRemoved = (quiz) => quiz.status.name === 'удалён';
const isRemoved = ref(fnIsRemoved(props.quiz));

const showModal = function(quiz) {
    removedQuiz.isRemoved = isRemoved.value;
    removedQuiz.id = quiz.id;
    removedQuiz.title = quiz.title;
    removedQuiz.show();
};

onUpdated(() => {
    isEditable.value = props.quiz.status.isEditable;
    isRemoved.value = fnIsRemoved(props.quiz);
});
</script>

<template>
    <td v-if="!isEditable && !isRemoved">
        <LockedTrashSvg title="Нельзя редактировать опрос" />
    </td>
    <td v-else>
        <ReplySvg v-if="isRemoved" title="Отменить статус 'удалён' опроса" @click="showModal(quiz)" />
        <TrashSvg v-else title="Перевести опрос в статус 'удалён'" @click="showModal(quiz)" />
    </td>
</template>
