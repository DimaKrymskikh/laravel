<script setup>
import { ref, reactive } from 'vue';
import { activeField, fieldModal } from '@/Services/Content/Quizzes/quizzes';
import { currentQuiz } from '@/Services/Content/Quizzes/quizCard';
import SimpleInput from '@/Components/Elements/Form/Input/SimpleInput.vue';
import CrossSvg from '@/Components/Svg/CrossSvg.vue';
import PencilSvg from '@/Components/Svg/PencilSvg.vue';
import LockedPencilSvg from '@/Components/Svg/Locked/LockedPencilSvg.vue';

const props = defineProps({
    quizItem: Object
});

const modal = reactive({ ...fieldModal });

const priority = ref(props.quizItem.priority);
const id = props.quizItem.id;
const field = 'priority';
const url = `/admin/quiz_items/${props.quizItem.id}`;

const hide = () => { modal.hideWithoutRequest(); };

const handler = () => { activeField.update(priority.value); };
</script>

<template>
    <td class="w-24 text-center" v-if="modal.isShow">
        <SimpleInput
            v-model="priority"
            :hide="hide"
            :handler="handler"
        />
    </td>
    <td class="w-24 text-center" v-else>{{ quizItem.priority ? quizItem.priority : 'не указан' }}</td>
        
    <td class="w-8" v-if="modal.isShow">
        <CrossSvg title="Закрыть"/>
    </td>
    <template v-else>
        <td class="w-8" @click="modal.show(id, field, url)" v-if="currentQuiz.isEditable && quizItem.status.isEditable">
            <PencilSvg title="Изменить приоритет вопроса" />
        </td>
        <td class="w-8" v-else>
            <LockedPencilSvg title="Нельзя изменить приоритет вопроса" />
        </td>
    </template>
</template>
