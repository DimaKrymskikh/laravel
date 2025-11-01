<script setup>
import { ref, reactive, onUpdated } from 'vue';
import { router } from '@inertiajs/vue3';
import { updateQuiz, updateQuizField } from '@/Services/Content/Quizzes/quizzes';
import SimpleInput from '@/Components/Elements/Form/Input/SimpleInput.vue';
import CrossSvg from '@/Components/Svg/CrossSvg.vue';
import PencilSvg from '@/Components/Svg/PencilSvg.vue';
import LockedPencilSvg from '@/Components/Svg/Locked/LockedPencilSvg.vue';

const props = defineProps({
    quiz: Object
});

const isEditable = ref(props.quiz.status.isEditable);

const updateQuizLeadTime = reactive({ ...updateQuiz, field: 'lead_time'});

const fieldValue = ref(props.quiz.lead_time);

const handlerUpdateQuiz = updateQuizField(props.quiz.id, fieldValue, updateQuizLeadTime);

const showInput = updateQuizLeadTime.show.bind(updateQuizLeadTime);

onUpdated(() => {
    isEditable.value = props.quiz.status.isEditable;
});
</script>

<template>
    <td v-if="updateQuizLeadTime.isShow">
        <SimpleInput
            v-model="fieldValue"
            :handler="handlerUpdateQuiz"
            :errorsMessage="updateQuizLeadTime.errorsMessage"
        />
    </td>
    <td v-else>{{ quiz.lead_time }} минут</td>
        
    <td class="w-8" @click="updateQuizLeadTime.hideOnCross" v-if="updateQuizLeadTime.isShow">
        <CrossSvg title="Закрыть"/>
    </td>
    <td class="w-8" v-else>
        <PencilSvg title="Изменить продолжительность опроса" @click="showInput" v-if="isEditable" />
        <LockedPencilSvg title="Нельзя изменить продолжительность опроса" v-else/>
    </td>
</template>
