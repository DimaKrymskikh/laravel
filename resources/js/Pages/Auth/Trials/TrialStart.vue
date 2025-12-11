<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { messageEmptyTable } from '@/Services/Content/Trials/trials';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import LockedButton from '@/Components/Buttons/Variants/LockedButton.vue';
import PrimaryButton from '@/Components/Buttons/Variants/PrimaryButton.vue';
import BreadCrumb from '@/Components/Elements/BreadCrumb.vue';
import EyeSvg from '@/Components/Svg/EyeSvg.vue';

const props = defineProps({
    user: Object,
    quiz: Object,
    errors: Object 
});

const titlePage = props.quiz.title;

// Список для хлебных крошек
const linksList = [{
            link: '/',
            text: 'Главная страница'
        }, {
            link: '/trials',
            text: 'Опросы'
        }, {
            text: titlePage
        }];
    
const startTrial = function() {
    router.post('/trials/start', {
            quiz_id: props.quiz.id
        }, {});
};
</script>

<template>
    <Head :title="titlePage" />
    <AuthLayout :errors="errors" :user="user">
        <BreadCrumb :linksList="linksList" />
        <h1>{{ titlePage }}</h1>
        
        <div class="pb-2">
            <span class="text-orange-800">Описание: </span>
            <span>{{ quiz.description }}</span>
        </div>
        <div class="pb-8">
            <span class="text-orange-800">Продолжительность: </span>
            <span>{{ quiz.lead_time }} минут</span>
        </div>
        
        <LockedButton v-if="quiz.isActiveTrial"
            buttonText="Не завершен опрос"
        />
        <PrimaryButton v-else
            buttonText="Начать испытание"
            :handler="startTrial"
        />
        
        <Link v-if="quiz.isActiveTrial" class="text-orange-800" href="/trials/show_trial">
            Перейти к активному опросу
        </Link>
    </AuthLayout>
</template>
