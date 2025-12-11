<script setup>
import { ref, reactive } from 'vue';
import { activeField, fieldModal } from '@/Services/Content/Quizzes/quizzes';
import { currentQuizItem } from '@/Services/Content/Quizzes/quizItemCard';
import SimpleTextarea from '@/Components/Elements/Form/Textarea/SimpleTextarea.vue';
import CrossSvg from '@/Components/Svg/CrossSvg.vue';
import PencilSvg from '@/Components/Svg/PencilSvg.vue';
import LockedPencilSvg from '@/Components/Svg/Locked/LockedPencilSvg.vue';

const props = defineProps({
    answer: Object
});

const modal = reactive({ ...fieldModal });

const id = props.answer.id;
const description = ref(props.answer.description);
const field = 'description';
const url = `/admin/quiz_answers/${props.answer.id}`;

const hide = () => { modal.hideWithoutRequest(); };

const handler = () => { activeField.update(description.value); };
</script>

<template>
    <td v-if="modal.isShow">
        <SimpleTextarea
            v-model="description"
            :handler="handler"
            :hide="hide"
        />
    </td>
    <td v-else>{{ answer.description }}</td>
        
    <td class="w-8" v-if="modal.isShow">
        <CrossSvg title="Закрыть"/>
    </td>
    <template v-else>
        <td class="w-8" @click="modal.show(id, field, url)" v-if="currentQuizItem.isEditable">
            <PencilSvg title="Изменить текст ответа" />
        </td>
        <td class="w-8" v-else>
            <LockedPencilSvg title="Нельзя изменить текст ответа" />
        </td>
    </template>
</template>
