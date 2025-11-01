<script setup>
import { ref, reactive } from 'vue';
import { router } from '@inertiajs/vue3';
import { app } from '@/Services/app';
import { defaultOnBefore, defaultOnError, defaultOnFinish } from '@/Services/router';
import { currentQuizItem } from '@/Services/Content/Quizzes/quizItemCard';
import SimpleTextarea from '@/Components/Elements/Form/Textarea/SimpleTextarea.vue';
import CrossSvg from '@/Components/Svg/CrossSvg.vue';
import PencilSvg from '@/Components/Svg/PencilSvg.vue';
import LockedPencilSvg from '@/Components/Svg/Locked/LockedPencilSvg.vue';

const { answer } = defineProps({
    answer: Object
});

const fieldValue = ref(answer.description);

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

const updateDescription = () => {
    router.put(`/admin/quiz_answers/${answer.id}`, {
        field: 'description',
        value: fieldValue.value
    }, {
        preserveScroll: true,
        onBefore: defaultOnBefore,
        onSuccess,
        onError: defaultOnError(hide),
        onFinish: defaultOnFinish
    });
};
</script>

<template>
    <td v-if="isShow">
        <SimpleTextarea
            v-model="fieldValue"
            :handler="updateDescription"
        />
    </td>
    <td v-else>{{ answer.description }}</td>
        
    <td class="w-8" @click="hideOnCross" v-if="isShow">
        <CrossSvg title="Закрыть"/>
    </td>
    <template v-else>
        <td class="w-8" @click="show" v-if="currentQuizItem.isEditable">
            <PencilSvg title="Изменить текст ответа" />
        </td>
        <td class="w-8" v-else>
            <LockedPencilSvg title="Нельзя изменить текст ответа" />
        </td>
    </template>
</template>
