<script setup>
import { inject } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import BreadCrumb from '@/Components/Elements/BreadCrumb.vue';

const { film } = defineProps({
    film: Object,
    user: Object,
    errors: Object | null
});

const titlePage = film.title;

const filmsAccount = inject('filmsAccount');

// Список для хлебных крошек
const linksList = [{
            link: '/',
            text: 'Главная страница'
        }, {
            link: filmsAccount.getUrl(),
            text: 'ЛК'
        }, {
            text: titlePage
        }];
</script>

<template>
    <Head :title="titlePage" />
    <AuthLayout :errors="errors" :user="user">
        <BreadCrumb :linksList="linksList" />
        <h1>{{ titlePage }}</h1>
        
        <div class="flex">
            <div class="w-1/4 pr-4">
                <h3>Основная информация</h3>
            </div>
            <div class="w-1/2 px-4">
                <h3>Описание</h3>
            </div>
            <div class="w-1/4 pl-4">
                <h3>Актёры</h3>
            </div>
        </div>

        <div class="flex">
            <div class="w-1/4 pr-4">
                <div>Фильм вышел в {{ film.release_year }} году</div>
                <div>Язык фильма: {{ film.language.name }}</div>
            </div>
            <div class="w-1/2 px-4">
                <div>{{ film.description }}</div>
            </div>
            <div class="w-1/4 pl-4">
                <div v-for="actor in film.actors">{{ actor.first_name }} {{ actor.last_name}}</div>
            </div>
        </div>
        
    </AuthLayout>
</template>
