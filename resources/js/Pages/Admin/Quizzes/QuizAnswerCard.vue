<script setup>
import { Head } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue';
import BreadCrumb from '@/Components/Elements/BreadCrumb.vue';
import QuizAnswerCardHeader from '@/Components/Pages/Admin/Quizzes/QuizAnswerCard/QuizAnswerCardHeader.vue';
import CheckSvg from '@/Components/Svg/CheckSvg.vue';

const props = defineProps({
    quizAnswer: Object,
    errors: Object
});

const titlePage = 'Карточка ответа';

const linksList = [{
            link: '/admin',
            text: 'Страница админа'
        }, {
            link: '/admin/quizzes',
            text: 'Опросы'
        }, {
            link: '/admin/quizzes/' + props.quizAnswer.quiz_item.quiz.id,
            text: 'Карточка опроса'
        }, {
            link: '/admin/quiz_items/' + props.quizAnswer.quiz_item.id,
            text: 'Карточка вопроса'
        }, {
            text: titlePage
        }];
</script>

<template>
    <Head :title="titlePage" />
    <AdminLayout>
        <BreadCrumb :linksList="linksList" />
        
        <QuizAnswerCardHeader :quizAnswer="quizAnswer" />
        
        <h1>{{ titlePage }}</h1>
        
        <div class="mb-4">
            <span class="text-orange-800">Текст ответа: </span>
            <span>{{ quizAnswer.description }}</span>
        </div>
        
        <div class="mb-4">
            <span class="text-orange-800">Правильный: </span>
            <span>
                <CheckSvg v-if="quizAnswer.is_correct" />
            </span>
        </div>
    </AdminLayout>
</template>
