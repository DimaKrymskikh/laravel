<script setup>
import { ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AccountLayout from '@/Layouts/Auth/AccountLayout.vue';
import { messageEmptyTableForResults } from '@/Services/Content/Trials/trials';

const props = defineProps({
    user: Object,
    trials: Object,
    errors: Object 
});

const titlePage = 'Результаты опросов';

const linksList = [{
            link: '/',
            text: 'Главная страница'
        }, {
            text: titlePage
        }];
</script>

<template>
    <Head :title="titlePage" />
    <AccountLayout :errors="errors" :user="user" :linksList="linksList">
        <h1>{{ titlePage }}</h1>
        
        <table class="container">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Название</th>
                    <th class="w-12">Число вопросов</th>
                    <th class="w-12">Число правильных ответов</th>
                    <th>Оценка</th>
                    <th>Дата</th>
                    <th>Продол-ть</th>
                </tr>
            </thead>
            <tbody v-if="props.trials.length">
                <tr v-for="(trial, index) in props.trials" :key="trial.id">
                    <td class="text-center">{{ index + 1 }}</td>
                    <td>{{ trial.title }}</td>
                    <td class="text-center">{{ trial.total_quiz_items }}</td>
                    <td class="text-center">{{ trial.correct_answers_number }}</td>
                    <td class="text-center">{{ trial.grade }}</td>
                    <td class="text-center font-sans">{{ trial.start_at }}</td>
                    <td class="text-center">{{ trial.lead_time }} минут</td>
                </tr>
            </tbody>
        </table>
        <div v-if="!props.trials.length" class="my-4 text-center text-orange-800">{{ messageEmptyTableForResults }}</div>
    </AccountLayout>
</template>
