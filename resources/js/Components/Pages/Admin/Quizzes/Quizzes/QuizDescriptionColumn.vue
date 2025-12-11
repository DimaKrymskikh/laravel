<script setup>
import { ref, reactive, onUpdated } from 'vue';
import { activeField, fieldModal } from '@/Services/Content/Quizzes/quizzes';
import SimpleTextarea from '@/Components/Elements/Form/Textarea/SimpleTextarea.vue';
import CrossSvg from '@/Components/Svg/CrossSvg.vue';
import PencilSvg from '@/Components/Svg/PencilSvg.vue';
import LockedPencilSvg from '@/Components/Svg/Locked/LockedPencilSvg.vue';

const props = defineProps({
    quiz: Object
});

const isEditable = ref(props.quiz.status.isEditable);

const modal = reactive({ ...fieldModal });

const description = ref(props.quiz.description);
const id = props.quiz.id;
const field = 'description';
const url = `/admin/quizzes/${props.quiz.id}`;

const hide = () => { modal.hideWithoutRequest(); };

const handler = () => { activeField.update(description.value); };

onUpdated(() => {
    isEditable.value = props.quiz.status.isEditable;
});
</script>

<template>
    <td v-if="modal.isShow">
        <SimpleTextarea
            v-model="description"
            :hide="hide"
            :handler="handler"
            :errorsMessage="activeField.errorsMessage"
        />
    </td>
    <td v-else>{{ quiz.description }}</td>

    <td class="w-8" v-if="modal.isShow">
        <CrossSvg title="Закрыть"/>
    </td>
    <td class="w-8" v-else>
        <PencilSvg title="Изменить описание опроса" @click="modal.show(id, field, url)" v-if="isEditable" />
        <LockedPencilSvg title="Нельзя изменить описание опроса" v-else/>
    </td>
</template>
