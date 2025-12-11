<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { approvedQuiz, removedQuiz } from '@/Services/Content/Quizzes/quizzes';
import { messageEmptyTable } from '@/Services/Content/Quizzes/quizzes';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import BreadCrumb from '@/Components/Elements/BreadCrumb.vue';
import AddQuizBlock from '@/Components/Pages/Admin/Quizzes/Quizzes/AddQuizBlock.vue';
import QuizDescriptionColumn from '@/Components/Pages/Admin/Quizzes/Quizzes/QuizDescriptionColumn.vue';
import QuizLeadTimeColumn from '@/Components/Pages/Admin/Quizzes/Quizzes/QuizLeadTimeColumn.vue';
import QuizTitleColumn from '@/Components/Pages/Admin/Quizzes/Quizzes/QuizTitleColumn.vue';
import ApprovedQuizColumn from '@/Components/Pages/Admin/Quizzes/Quizzes/ApprovedQuizColumn.vue';
import ApprovedQuizModal from '@/Components/Pages/Admin/Quizzes/Quizzes/ApprovedQuizModal.vue';
import RemovedQuizColumn from '@/Components/Pages/Admin/Quizzes/Quizzes/RemovedQuizColumn.vue';
import RemovedQuizModal from '@/Components/Pages/Admin/Quizzes/Quizzes/RemovedQuizModal.vue';
import EyeSvg from '@/Components/Svg/EyeSvg.vue';

const props = defineProps({
    quizzes: Object,
    errors: Object
});

const titlePage = 'Опросы';

// Список для хлебных крошек
const linksList = [{
            link: '/admin',
            text: 'Страница админа'
        }, {
            text: titlePage
        }];
</script>

<template>
    <Head :title="titlePage" />
    <AdminLayout>
        <BreadCrumb :linksList="linksList" />
        <h1>{{ titlePage }}</h1>
        
        <div class="my-4">
            <AddQuizBlock />
        </div>
        
        <table class="container">
            <thead>
                <tr>
                    <th>#</th>
                    <th colspan="2">Название</th>
                    <th colspan="2">Описание</th>
                    <th colspan="2">Прод-ть</th>
                    <th>Статус</th>
                    <th class="w-8"></th>
                    <th class="w-8"></th>
                    <th class="w-8"></th>
                </tr>
            </thead>
            <tbody v-if="props.quizzes.length">
                <tr v-for="(quiz, index) in props.quizzes" :key="quiz.id">
                    <td class="text-center">{{ index + 1 }}</td>
                    <QuizTitleColumn :quiz="quiz" />
                    <QuizDescriptionColumn :quiz="quiz" />
                    <QuizLeadTimeColumn :quiz="quiz" />
                    <td class="text-center" :class="quiz.status.style">
                        {{ quiz.status.name }}
                    </td>
                    <td>
                        <Link :href="`/admin/quizzes/${quiz.id}`">
                            <EyeSvg title="Открыть карточку опроса"/>
                        </Link>
                    </td>
                    <ApprovedQuizColumn :quiz="quiz"/>
                    <RemovedQuizColumn :quiz="quiz"/>
                </tr>
            </tbody>
        </table>
        <div  v-if="!props.quizzes.length" class="my-4 text-center text-orange-800">{{ messageEmptyTable }}</div>
        
        <ApprovedQuizModal v-if="approvedQuiz.isShow" />
        <RemovedQuizModal v-if="removedQuiz.isShow" />
    </AdminLayout>
</template>
