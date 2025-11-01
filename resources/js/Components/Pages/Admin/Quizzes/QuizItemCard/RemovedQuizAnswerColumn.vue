<script setup>
import { ref, onUpdated } from 'vue';
import { currentQuizItem, removedQuizAnswer } from '@/Services/Content/Quizzes/quizItemCard';
import TrashSvg from '@/Components/Svg/TrashSvg.vue';
import LockedTrashSvg from '@/Components/Svg/Locked/LockedTrashSvg.vue';

const props = defineProps({
    answer: Object
});

const showModal = (answer) => {
    removedQuizAnswer.id = answer.id;
    removedQuizAnswer.description = answer.description;
    removedQuizAnswer.show();
};
</script>

<template>
    <td @click="showModal(answer)" v-if="currentQuizItem.isEditable">
        <TrashSvg title="Удалить ответ" />
    </td>
    <td v-else>
        <LockedTrashSvg title="Нельзя удалить ответ" />
    </td>
</template>
