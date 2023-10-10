<script setup>
import { ref, inject } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import BreadCrumb from '@/Components/Elements/BreadCrumb.vue';
import Dropdown from '@/Components/Elements/Dropdown.vue';
import Buttons from '@/Components/Pagination/Buttons.vue';
import Info from '@/Components/Pagination/Info.vue';
import CheckCircleSvg from '@/Components/Svg/CheckCircleSvg.vue';
import PlusCircleSvg from '@/Components/Svg/PlusCircleSvg.vue';
import Spinner from '@/components/Svg/Spinner.vue';

const { films } = defineProps({
    films: Object,
    user: Object,
    errors: Object | null
});

const app = inject('app');

const filmsList = inject('filmsList');
filmsList.page = films.current_page;

const titlePage = 'Каталог';

// id фильма, который добавляется в коллекцию пользователя
const filmId = ref(null);

// Список для хлебных крошек
const linksList = [{
            link: '/',
            text: 'Главная страница'
        }, {
            text: 'Каталог'
        }];

/**
 * Добавляет фильм в коллекцию пользователя
 * @param {Element} tag
 * @returns {undefined}
 */
const addFilm = function(tag) {
    const td = tag.closest('td');
    // Защита от повторного клика
    td.classList.remove('add-film');
    
    filmId.value = td.getAttribute('data-film_id');

    router.post(`account/addfilm/${filmId.value}`, {
            page: filmsList.page,
            number: filmsList.perPage,
            title: filmsList.title,
            description: filmsList.description
        }, {
            preserveScroll: true,
            onBefore: () => app.isRequest = true,
            onFinish: () => app.isRequest = false
        });
};

/**
 * Управляет изменениями в таблице фильмов
 * @param {Event} e
 * @returns {undefined}
 */
const handlerTableChange = function(e) {
    if (e.target.closest('td') && e.target.closest('td').classList.contains('add-film')) {
        addFilm(e.target);
    }
};

// Изменяет число фильмов на странице
const changeNumberOfFilmsOnPage = function(newNumber) {
    filmsList.page = 1;
    filmsList.perPage = newNumber;
    router.get(filmsList.getUrl('/films'));
};

const putFilms = function(e) {
    if(e.key.toLowerCase() !== "enter") {
        return;
    }
    
    filmsList.page = 1;
    router.get(filmsList.getUrl('/films'));
};
</script>

<template>
    <Head :title="titlePage" />
    <AuthLayout :errors="errors" :user="user">
        <BreadCrumb :linksList="linksList" />
        <h1>{{ titlePage }}</h1>
        
        <div class="flex justify-between pb-4">
            <Dropdown
                buttonName="Число фильмов на странице"
                :itemsNumberOnPage="films.per_page"
                :changeNumber="changeNumberOfFilmsOnPage"
            />
        </div>
        
        <table class="container" @click="handlerTableChange">
            <caption>
                <Info :films='films' v-if="films.total > 0" />
            </caption>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Название</th>
                    <th>Описание</th>
                    <th>Язык</th>
                    <th></th>
                </tr>
                <tr>
                    <th></th>
                    <th><input type="text" v-model="filmsList.title" @keyup="putFilms"></th>
                    <th><input type="text" v-model="filmsList.description" @keyup="putFilms"></th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(film, index) in films.data" class="hover:bg-green-300">
                    <td>{{ films.from + index }}</td>
                    <td>{{ film.title }}</td>
                    <td>{{ film.description }}</td>
                    <td>{{ film.language.name }}</td>
                    <td
                        :class="film.isAvailable ? null : 'add-film'"
                        :data-film_id="film.isAvailable ? null : film.id"
                    >
                        <Spinner styleSpinner="h-4" class="flex justify-center" v-if="app.isRequest && filmId == film.id" />
                        <template v-else>
                            <CheckCircleSvg v-if="film.isAvailable" title="Данный фильм уже в вашей коллекции" />
                            <PlusCircleSvg v-else title="Добавить фильм в коллекцию" />
                        </template>
                    </td>
                </tr>
            </tbody>
        </table>
        
        <Buttons :links="films.links" v-if="films.total > 0" />
    </AuthLayout>
</template>