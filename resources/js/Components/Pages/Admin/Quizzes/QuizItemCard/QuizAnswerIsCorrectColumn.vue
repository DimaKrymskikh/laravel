<script setup>
import { ref, reactive, watch } from 'vue';
import { activeField, fieldModal } from '@/Services/Content/Quizzes/quizzes';
import { currentQuizItem } from '@/Services/Content/Quizzes/quizItemCard';
import Checkbox from '@/components/Elements/Form/Checkbox.vue';
import CrossSvg from '@/Components/Svg/CrossSvg.vue';
import PencilSvg from '@/Components/Svg/PencilSvg.vue';
import CheckSvg from '@/Components/Svg/CheckSvg.vue';
import LockedPencilSvg from '@/Components/Svg/Locked/LockedPencilSvg.vue';

const props = defineProps({
    answer: Object
});

const modal = reactive({ ...fieldModal });

const isCorrect = ref(props.answer.is_correct);
const id = props.answer.id;
const field = 'is_correct';
const url = `/admin/quiz_answers/${props.answer.id}`;

const hide = () => { modal.hideWithoutRequest(); };

const handler = () => { activeField.update(isCorrect.value); };
</script>

<template>
    <td class="w-24" v-if="modal.isShow">
        <Checkbox 
            titleText="Ответ правильный:"
            v-model="isCorrect"
            :handler="handler"
            :hide="hide"
        />
    </td>
    <td class="w-24" v-else><CheckSvg v-if="answer.is_correct" /></td>
        
    <td class="w-8" v-if="modal.isShow">
        <CrossSvg title="Закрыть"/>
    </td>
    <template v-else>
        <td class="w-8" @click="modal.show(id, field, url)" v-if="currentQuizItem.isEditable">
            <PencilSvg title="Изменить правильность ответа" />
        </td>
        <td class="w-8" v-else>
            <LockedPencilSvg title="Нельзя изменить правильность ответа" />
        </td>
    </template>
</template>
