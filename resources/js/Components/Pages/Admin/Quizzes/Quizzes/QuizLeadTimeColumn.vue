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

const leadTime = ref(props.quiz.lead_time);
const id = props.quiz.id;
const field = 'lead_time';
const url = `/admin/quizzes/${props.quiz.id}`;

const hide = () => { modal.hideWithoutRequest(); };

const handler = () => { activeField.update(leadTime.value); };

onUpdated(() => {
    isEditable.value = props.quiz.status.isEditable;
});
</script>

<template>
    <td v-if="modal.isShow">
        <SimpleInput
            v-model="leadTime"
            :hide="hide"
            :handler="handler"
            :errorsMessage="activeField.errorsMessage"
        />
    </td>
    <td v-else>{{ quiz.lead_time }} минут</td>
        
    <td class="w-8" v-if="modal.isShow">
        <CrossSvg title="Закрыть"/>
    </td>
    <td class="w-8" v-else>
        <PencilSvg title="Изменить продолжительность опроса" @click="modal.show(id, field, url)" v-if="isEditable" />
        <LockedPencilSvg title="Нельзя изменить продолжительность опроса" v-else/>
    </td>
</template>
