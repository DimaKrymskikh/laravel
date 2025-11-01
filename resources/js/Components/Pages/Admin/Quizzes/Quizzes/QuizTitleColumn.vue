<script setup>
import { ref, reactive, onUpdated } from 'vue';
import { router } from '@inertiajs/vue3';
import { app } from '@/Services/app';
import { updateQuiz, updateQuizField } from '@/Services/Content/Quizzes/quizzes';
import SimpleInput from '@/Components/Elements/Form/Input/SimpleInput.vue';
import CrossSvg from '@/Components/Svg/CrossSvg.vue';
import PencilSvg from '@/Components/Svg/PencilSvg.vue';
import LockedPencilSvg from '@/Components/Svg/Locked/LockedPencilSvg.vue';

const props = defineProps({
    quiz: Object
});

const isEditable = ref(props.quiz.status.isEditable);

const updateQuizTitle = reactive({ ...updateQuiz, field: 'title'});

const fieldValue = ref(props.quiz.title);

const handlerUpdateQuiz = updateQuizField(props.quiz.id, fieldValue, updateQuizTitle);

const showInput = updateQuizTitle.show.bind(updateQuizTitle);

onUpdated(() => {
    isEditable.value = props.quiz.status.isEditable;
});
</script>

<template>
        <td class="reletive" v-if="updateQuizTitle.isShow">
            <SimpleInput
                v-model="fieldValue"
                :handler="handlerUpdateQuiz"
                :errorsMessage="updateQuizTitle.errorsMessage"
            />
        </td>
        <td v-else>{{ quiz.title }}</td>
        
        <td class="w-8" @click="updateQuizTitle.hideOnCross" v-if="updateQuizTitle.isShow">
            <CrossSvg title="Закрыть"/>
        </td>
        <td class="w-8" v-else>
            <PencilSvg title="Изменить название опроса" @click="showInput" v-if="isEditable" />
            <LockedPencilSvg title="Нельзя изменить название опроса" v-else/>
        </td>
</template>
