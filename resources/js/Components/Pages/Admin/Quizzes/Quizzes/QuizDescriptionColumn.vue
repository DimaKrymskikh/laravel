<script setup>
import { ref, reactive, onUpdated } from 'vue';
import { router } from '@inertiajs/vue3';
import { updateQuiz, updateQuizField } from '@/Services/Content/Quizzes/quizzes';
import SimpleTextarea from '@/Components/Elements/Form/Textarea/SimpleTextarea.vue';
import CrossSvg from '@/Components/Svg/CrossSvg.vue';
import PencilSvg from '@/Components/Svg/PencilSvg.vue';
import LockedPencilSvg from '@/Components/Svg/Locked/LockedPencilSvg.vue';

const props = defineProps({
    quiz: Object
});

const isEditable = ref(props.quiz.status.isEditable);

const updateQuizDescription = reactive({ ...updateQuiz, field: 'description'});

const fieldValue = ref(props.quiz.description);

const handlerUpdateQuiz = updateQuizField(props.quiz.id, fieldValue, updateQuizDescription);

const showInput = updateQuizDescription.show.bind(updateQuizDescription);

onUpdated(() => {
    isEditable.value = props.quiz.status.isEditable;
});
</script>

<template>
    <td v-if="updateQuizDescription.isShow">
        <SimpleTextarea
            v-model="fieldValue"
            :handler="handlerUpdateQuiz"
            :errorsMessage="updateQuizDescription.errorsMessage"
        />
    </td>
    <td v-else>{{ quiz.description }}</td>

    <td class="w-8" @click="updateQuizDescription.hideOnCross" v-if="updateQuizDescription.isShow">
        <CrossSvg title="Закрыть"/>
    </td>
    <td class="w-8" v-else>
        <PencilSvg title="Изменить описание опроса" @click="showInput" v-if="isEditable" />
        <LockedPencilSvg title="Нельзя изменить описание опроса" v-else/>
    </td>
</template>
