<script setup>
import { ref, reactive } from 'vue';
import { activeField, fieldModal } from '@/Services/Content/Quizzes/quizzes';
import { currentQuizItem } from '@/Services/Content/Quizzes/quizItemCard';
import SimpleInput from '@/Components/Elements/Form/Input/SimpleInput.vue';
import CrossSvg from '@/Components/Svg/CrossSvg.vue';
import PencilSvg from '@/Components/Svg/PencilSvg.vue';
import LockedPencilSvg from '@/Components/Svg/Locked/LockedPencilSvg.vue';

const props = defineProps({
    answer: Object
});

const modal = reactive({ ...fieldModal });

const priority = ref(props.answer.priority);
const id = props.answer.id;
const field = 'priority';
const url = `/admin/quiz_answers/${props.answer.id}`;

const hide = () => { modal.hideWithoutRequest(); };

const handler = () => { activeField.update(priority.value); };
</script>

<template>
    <td class="w-24 text-center" v-if="modal.isShow">
        <SimpleInput
            v-model="priority"
            :handler="handler"
            :hide="hide"
        />
    </td>
    <td class="w-24 text-center" v-else>{{ answer.priority ? answer.priority : 'не указан'}}</td>
        
    <td class="w-8" v-if="modal.isShow">
        <CrossSvg title="Закрыть"/>
    </td>
    <template v-else>
        <td class="w-8" @click="modal.show(id, field, url)" v-if="currentQuizItem.isEditable">
            <PencilSvg title="Изменить приоритет ответа" />
        </td>
        <td class="w-8" v-else>
            <LockedPencilSvg title="Нельзя изменить приоритет ответа" />
        </td>
    </template>
</template>
