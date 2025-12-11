<script setup>
import { ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import { messageEmptyTable } from '@/Services/Content/Trials/trials';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import BreadCrumb from '@/Components/Elements/BreadCrumb.vue';
import EyeSvg from '@/Components/Svg/EyeSvg.vue';

const props = defineProps({
    user: Object,
    quizzes: Object,
    errors: Object 
});

const titlePage = 'Опросы';

// Список для хлебных крошек
const linksList = [{
            link: '/',
            text: 'Главная страница'
        }, {
            text: titlePage
        }];
</script>

<template>
    <Head :title="titlePage" />
    <AuthLayout :errors="errors" :user="user">
        <BreadCrumb :linksList="linksList" />
        <h1>{{ titlePage }}</h1>
        
        <table class="container">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Название</th>
                    <th>Описание</th>
                    <th>Прод-ть</th>
                    <th class="w-8"></th>
                </tr>
            </thead>
            <tbody v-if="props.quizzes.length">
                <tr v-for="(quiz, index) in props.quizzes" :key="quiz.id">
                    <td class="text-center">{{ index + 1 }}</td>
                    <td>{{ quiz.title }}</td>
                    <td>{{ quiz.description }}</td>
                    <td>{{ quiz.lead_time }} минут</td>
                    <td>
                        <Link :href="`/trials/${quiz.id}`">
                            <EyeSvg title="Выбрать опрос"/>
                        </Link>
                    </td>
                </tr>
            </tbody>
        </table>
        <div v-if="!props.quizzes.length" class="my-4 text-center text-orange-800">{{ messageEmptyTable }}</div>
    </AuthLayout>
</template>
