<script setup>
import { onMounted, ref, watch } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import { useCountdown } from '@/Services/app';
import { trialQuestions } from '@/Services/Content/Trials/trials';
import IsolatedLayout from '@/Layouts/IsolatedLayout.vue';
import ChooseAnswerModal from '@/Components/Pages/Auth/Trials/TrialPage/ChooseAnswerModal.vue';
import FormButton from '@/Components/Elements/FormButton.vue';
import PencilSvg from '@/Components/Svg/PencilSvg.vue';

const props = defineProps({
    user: Object,
    trial: Object,
    quizItems: Object,
    errors: Object 
});

const titlePage = props.trial.title;

const time = props.trial.start_at_seconds - Math.floor(Date.now() / 1000) + (props.trial.lead_time * 60);
const countdown = useCountdown(time);

const handlerComplete = function() {
    router.post('/trials/complete', {
            trial_id: props.trial.id
        }, {});
};

onMounted(() => {
    if (countdown.timeInSeconds.value > 0) {
        countdown.startTimer();
    } else {
        handlerComplete();
    }
});

watch(countdown.timeInSeconds, () => {
    if(countdown.timeInSeconds.value > 0) {
        return;
    }
    handlerComplete();
});
</script>

<template>
    <Head :title="titlePage" />
    <IsolatedLayout>
        <nav id="main-nav" class="bg-orange-300 shadow shadow-orange-200 py-1">
            <div class="lg:container text-orange-900 flex justify-between">
                <span>Опрос для {{ user.login }}</span>
                <span>
                    До окончания опроса осталось: <span class="font-sans">{{ countdown.formattedTime.value }}</span>
                </span>
            </div>
        </nav>

        <main class="lg:container">
            <h1>{{ titlePage }}</h1>
            <h2>Вопросы:</h2>
        
            <table class="container">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Вопрос</th>
                        <th>Ответ</th>
                        <th class="w-8"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(answer, index) in trial.answers" :key="answer.quiz_item_id">
                        <td class="text-center">{{ index + 1 }}</td>
                        <td>{{ answer.question }}</td>
                        <td>{{ answer.answer ? answer.answer : 'не дан'}}</td>
                        <td>
                            <PencilSvg title="Выбрать ответ" @click="trialQuestions.show(answer)" />
                        </td>
                    </tr>
                </tbody>
            </table>

            <FormButton class="w-56 small-caps my-8" text="завершить опрос" @click.prevent="handlerComplete"/>
            
            <ChooseAnswerModal
                v-if="trialQuestions.isShow"
                :quizItems="quizItems"
            />
        </main>

    </IsolatedLayout>
</template>
