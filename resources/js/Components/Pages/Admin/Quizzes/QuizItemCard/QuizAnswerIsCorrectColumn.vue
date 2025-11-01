<script setup>
import { ref, reactive, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import { app } from '@/Services/app';
import { defaultOnBefore, defaultOnError, defaultOnFinish } from '@/Services/router';
import { currentQuizItem } from '@/Services/Content/Quizzes/quizItemCard';
import Checkbox from '@/components/Elements/Form/Checkbox.vue';
import CrossSvg from '@/Components/Svg/CrossSvg.vue';
import PencilSvg from '@/Components/Svg/PencilSvg.vue';
import CheckSvg from '@/Components/Svg/CheckSvg.vue';
import LockedPencilSvg from '@/Components/Svg/Locked/LockedPencilSvg.vue';

const { answer } = defineProps({
    answer: Object
});

const fieldValue = ref(answer.is_correct);

const isShow = ref(false);

const hide = () => {
    isShow.value = false;
};

const show = () => {
    isShow.value = true;
};

const hideOnCross = () => {
    if (!app.isRequest) {
        hide();
    }
};
    
const onSuccess = () => { hide(); };

const updateСorrect = () => {
    router.put(`/admin/quiz_answers/${answer.id}`, {
        field: 'is_correct',
        value: fieldValue.value
    }, {
        preserveScroll: true,
        onBefore: defaultOnBefore,
        onSuccess,
        onError: defaultOnError(hide),
        onFinish: defaultOnFinish
    });
};

watch(fieldValue, updateСorrect);
</script>

<template>
    <td v-if="isShow">
        <Checkbox 
            titleText="Ответ правильный:"
            v-model="fieldValue"
        />
    </td>
    <td v-else><CheckSvg v-if="answer.is_correct" /></td>
        
    <td class="w-8" @click="hideOnCross" v-if="isShow">
        <CrossSvg title="Закрыть"/>
    </td>
    <template v-else>
        <td class="w-8" @click="show" v-if="currentQuizItem.isEditable">
            <PencilSvg title="Изменить правильность ответа" />
        </td>
        <td class="w-8" v-else>
            <LockedPencilSvg title="Нельзя изменить правильность ответа" />
        </td>
    </template>
</template>
