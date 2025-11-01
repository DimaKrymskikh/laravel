<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { currentQuiz, messageEmptyTable, removedQuizItem } from '@/Services/Content/Quizzes/quizCard';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import QuizItemDescriptionColumn from '@/Components/Pages/Admin/Quizzes/QuizCard/QuizItemDescriptionColumn.vue';
import BreadCrumb from '@/Components/Elements/BreadCrumb.vue';
import AddQuizItemBlock from '@/Components/Pages/Admin/Quizzes/QuizCard/AddQuizItemBlock.vue';
import RemovedQuizItemColumn from '@/Components/Pages/Admin/Quizzes/QuizCard/RemovedQuizItemColumn.vue';
import RemovedQuizItemModal from '@/Components/Pages/Admin/Quizzes/QuizCard/RemovedQuizItemModal.vue';
import EyeSvg from '@/Components/Svg/EyeSvg.vue';

const props = defineProps({
    quiz: Object,
    errors: Object
});

currentQuiz.setIsEditable(props.quiz);

const titlePage = props.quiz.title;

// Список для хлебных крошек
const linksList = [{
            link: '/admin',
            text: 'Страница админа'
        }, {
            link: '/admin/quizzes',
            text: 'Опросы'
        }, {
            text: titlePage
        }];
</script>

<template>
    <Head :title="titlePage" />
    <AdminLayout>
        <BreadCrumb :linksList="linksList" />
        <h1>{{ titlePage }}</h1>
        
        <div class="mb-4">
            <span class="text-orange-800">Описание опроса: </span>
            <span>{{ quiz.description }}</span>
        </div>

        <div class="mb-4">
            <span class="text-orange-800">Статус опроса: </span>
            <span :class="quiz.status.style">{{ quiz.status.name }}</span>
        </div>
        
        <div class="mb-4">
            <AddQuizItemBlock :quiz="quiz" />
        </div>
        
        <table class="container">
            <thead>
                <tr>
                    <th>#</th>
                    <th colspan="2">Вопрос</th>
                    <th>Статус</th>
                    <th class="w-8"></th>
                    <th class="w-8"></th>
                </tr>
            </thead>
            <tbody v-if="quiz.quiz_items.length">
                <tr v-for="(item, index) in quiz.quiz_items" :key="item.id">
                    <td class="text-center">{{ index + 1 }}</td>
                    <QuizItemDescriptionColumn :quizItem="item"/>
                    <td class="text-center" :class="item.status.style">
                        {{ item.status.name }}
                    </td>
                    <td>
                        <Link :href="`/admin/quiz_items/${item.id}`">
                            <EyeSvg title="Открыть карточку вопроса"/>
                        </Link>
                    </td>
                    <RemovedQuizItemColumn :quizItem="item"/>
                </tr>
            </tbody>
        </table>
        <div v-if="!quiz.quiz_items.length" class="my-4 text-center text-orange-800">{{ messageEmptyTable }}</div>
        
        <RemovedQuizItemModal v-if="removedQuizItem.isShow" />
    </AdminLayout>
</template>
