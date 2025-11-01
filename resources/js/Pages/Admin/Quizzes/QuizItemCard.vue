<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { currentQuizItem, messageEmptyTable, removedQuizAnswer } from '@/Services/Content/Quizzes/quizItemCard';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import QuizAnswerDescriptionColumn from '@/Components/Pages/Admin/Quizzes/QuizItemCard/QuizAnswerDescriptionColumn.vue';
import QuizAnswerIsCorrectColumn from '@/Components/Pages/Admin/Quizzes/QuizItemCard/QuizAnswerIsCorrectColumn.vue';
import BreadCrumb from '@/Components/Elements/BreadCrumb.vue';
import AddQuizAnswerBlock from '@/Components/Pages/Admin/Quizzes/QuizItemCard/AddQuizAnswerBlock.vue';
import RemovedQuizAnswerColumn from '@/Components/Pages/Admin/Quizzes/QuizItemCard/RemovedQuizAnswerColumn.vue';
import RemovedQuizAnswerModal from '@/Components/Pages/Admin/Quizzes/QuizItemCard/RemovedQuizAnswerModal.vue';
import CheckSvg from '@/Components/Svg/CheckSvg.vue';
import EyeSvg from '@/Components/Svg/EyeSvg.vue';
import TrashSvg from '@/Components/Svg/TrashSvg.vue';

const props = defineProps({
    quizItem: Object,
    errors: Object
});

currentQuizItem.setIsEditable(props.quizItem);

const titlePage = 'Карточка вопроса';

// Список для хлебных крошек
const linksList = [{
            link: '/admin',
            text: 'Страница админа'
        }, {
            link: '/admin/quizzes',
            text: 'Опросы'
        }, {
            link: '/admin/quizzes/' + props.quizItem.quiz.id,
            text: 'Карточка опроса'
        }, {
            text: titlePage
        }];
</script>

<template>
    <Head :title="titlePage" />
    <AdminLayout>
        <BreadCrumb :linksList="linksList" />
        
        <div class="mb-4">
            <span class="text-orange-800">Опрос: </span>
            <span>{{ quizItem.quiz.title }}</span>
        </div>

        <div class="mb-4">
            <span class="text-orange-800">Статус опроса: </span>
            <span :class="quizItem.quiz.status.style">{{ quizItem.quiz.status.name }}</span>
        </div>
        
        <h1>{{ titlePage }}</h1>
        
        <div class="mb-4">
            <span class="text-orange-800">Текст вопроса: </span>
            <span>{{ quizItem.description }}</span>
        </div>

        <div class="mb-4">
            <span class="text-orange-800">Статус вопроса: </span>
            <span :class="quizItem.status.style">{{ quizItem.status.name }}</span>
        </div>
        
        <div class="mb-4">
            <AddQuizAnswerBlock :quizItem="quizItem" />
        </div>
        
        <table class="container">
            <thead>
                <tr>
                    <th class="w-8">#</th>
                    <th colspan="2">Ответ</th>
                    <th colspan="2">Правильный</th>
                    <th class="w-8"></th>
                    <th class="w-8"></th>
                </tr>
            </thead>
            <tbody v-if="props.quizItem.answers.length">
                <tr v-for="(answer, index) in props.quizItem.answers" :key="answer.id">
                    <td class="text-center">{{ index + 1 }}</td>
                    <QuizAnswerDescriptionColumn :answer="answer"/>
                    <QuizAnswerIsCorrectColumn :answer="answer"/>
                    <td>
                        <Link :href="`/admin/quiz_answers/${answer.id}`">
                            <EyeSvg title="Открыть карточку ответа"/>
                        </Link>
                    </td>
                    <RemovedQuizAnswerColumn :answer="answer" />
                </tr>
            </tbody>
        </table>
        <div v-if="!props.quizItem.answers.length" class="my-4 text-center text-orange-800">{{ messageEmptyTable }}</div>
        
        <RemovedQuizAnswerModal :quizItem="quizItem" v-if="removedQuizAnswer.isShow" />
    </AdminLayout>
</template>
