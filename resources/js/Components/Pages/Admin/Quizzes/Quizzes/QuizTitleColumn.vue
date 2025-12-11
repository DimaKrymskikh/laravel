<script setup>
import { ref, reactive, onUpdated } from 'vue';
import { activeField, fieldModal } from '@/Services/Content/Quizzes/quizzes';
import SimpleInput from '@/Components/Elements/Form/Input/SimpleInput.vue';
import CrossSvg from '@/Components/Svg/CrossSvg.vue';
import PencilSvg from '@/Components/Svg/PencilSvg.vue';
import LockedPencilSvg from '@/Components/Svg/Locked/LockedPencilSvg.vue';

const props = defineProps({
    quiz: Object
});

const isEditable = ref(props.quiz.status.isEditable);

const modal = reactive({ ...fieldModal });

const title = ref(props.quiz.title);
const id = props.quiz.id;
const field = 'title';
const url = `/admin/quizzes/${props.quiz.id}`;

const hide = () => { modal.hideWithoutRequest(); };

const handler = () => { activeField.update(title.value); };

onUpdated(() => {
    isEditable.value = props.quiz.status.isEditable;
});
</script>

<template>
        <td class="reletive" v-if="modal.isShow">
            <SimpleInput
                v-model="title"
                :hide="hide"
                :handler="handler"
                :errorsMessage="activeField.errorsMessage"
            />
        </td>
        <td v-else>{{ quiz.title }}</td>
        
        <td class="w-8" v-if="modal.isShow">
            <CrossSvg title="Закрыть"/>
        </td>
        <td class="w-8" v-else>
            <PencilSvg title="Изменить название опроса" @click="modal.show(id, field, url)" v-if="isEditable" />
            <LockedPencilSvg title="Нельзя изменить название опроса" v-else/>
        </td>
</template>
