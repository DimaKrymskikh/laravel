<script setup>
import { inject } from 'vue';
import { Head } from '@inertiajs/vue3'
import { router } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import BreadCrumb from '@/Components/Elements/BreadCrumb.vue';
import Dropdown from '@/Components/Elements/Dropdown.vue';
import Buttons from '@/Components/Pagination/Buttons.vue';
import Info from '@/Components/Pagination/Info.vue';

const { films } = defineProps({
    films: Object,
    errors: Object
});

const titlePage = 'Фильмы';

// Список для хлебных крошек
const linksList = [{
            link: '/guest',
            text: 'Главная страница'
        }, {
            text: titlePage
        }];

const filmsList = inject('filmsList');
filmsList.page = films.current_page;

// Изменяет число фильмов на странице
const changeNumberOfFilmsOnPage = function(newNumber) {
    filmsList.page = 1;
    filmsList.perPage = newNumber;
    router.get(filmsList.getUrl('/guest/films'));
};

const putFilms = function(e) {
    if(e.key.toLowerCase() !== "enter") {
        return;
    }
    
    filmsList.page = 1;
    router.get(filmsList.getUrl('/guest/films'));
};
</script>

<template>
    <Head :title="titlePage" />
    <GuestLayout>
        <BreadCrumb :linksList="linksList" />
        <h1>{{ titlePage }}</h1>
        
        <div class="flex justify-between pb-4">
            <Dropdown
                buttonName="Число фильмов на странице"
                :itemsNumberOnPage="films.per_page"
                :changeNumber="changeNumberOfFilmsOnPage"
            />
        </div>
        
        <table class="container">
            <caption>
                <Info :films='films' v-if="films.total > 0" />
            </caption>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Название</th>
                    <th>Описание</th>
                    <th>Язык</th>
                </tr>
                <tr>
                    <th></th>
                    <th><input type="text" v-model="filmsList.title" @keyup="putFilms"></th>
                    <th><input type="text" v-model="filmsList.description" @keyup="putFilms"></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(film, index) in films.data">
                    <td>{{ films.from + index }}</td>
                    <td>{{ film.title }}</td>
                    <td>{{ film.description }}</td>
                    <td>{{ film.language ? film.language.name : '' }}</td>
                </tr>
            </tbody>
        </table>
        
        <Buttons :links="films.links" v-if="films.total > 0" />
    </GuestLayout>
</template>
