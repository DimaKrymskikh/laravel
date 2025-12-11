<script setup>
import { ref, reactive } from 'vue';
import { activeField, fieldModal } from '@/Services/Content/Quizzes/quizzes';
import { currentQuiz } from '@/Services/Content/Quizzes/quizCard';
import SimpleTextarea from '@/Components/Elements/Form/Textarea/SimpleTextarea.vue';
import CrossSvg from '@/Components/Svg/CrossSvg.vue';
import PencilSvg from '@/Components/Svg/PencilSvg.vue';
import LockedPencilSvg from '@/Components/Svg/Locked/LockedPencilSvg.vue';

const props = defineProps({
    quizItem: Object
});

const modal = reactive({ ...fieldModal });

const description = ref(props.quizItem.description);
const id = props.quizItem.id;
const field = 'description';
const url = `/admin/quiz_items/${props.quizItem.id}`;

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
    <td v-else>{{ quizItem.description }}</td>
        
    <td class="w-8" v-if="modal.isShow">
        <CrossSvg title="Закрыть"/>
    </td>
    <template v-else>
        <td class="w-8" @click="modal.show(id, field, url)" v-if="currentQuiz.isEditable && quizItem.status.isEditable">
            <PencilSvg title="Изменить текст вопроса" />
        </td>
        <td class="w-8" v-else>
            <LockedPencilSvg title="Нельзя изменить текст вопроса" />
        </td>
    </template>
</template>
