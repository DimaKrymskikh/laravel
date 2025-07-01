<script setup>
import { ref, inject, watch } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import { app } from '@/Services/app';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import BreadCrumb from '@/Components/Elements/BreadCrumb.vue';
import LangugeDropdown from '@/Components/Elements/Dropdowns/LanguageDropdown.vue';
import YearDropdown from '@/Components/Elements/Dropdowns/YearDropdown.vue';
import Dropdown from '@/Components/Elements/Dropdown.vue';
import Buttons from '@/Components/Pagination/Buttons.vue';
import Info from '@/Components/Pagination/Info.vue';
import CheckCircleSvg from '@/Components/Svg/CheckCircleSvg.vue';
import PlusCircleSvg from '@/Components/Svg/PlusCircleSvg.vue';
import Spinner from '@/Components/Svg/Spinner.vue';
import EchoAuth from '@/Components/Broadcast/EchoAuth.vue';

const { films } = defineProps({
    films: Object,
    user: Object,
    errors: Object
});

const filmsList = inject('filmsList');
filmsList.setOptions(films);

const languageName = ref(filmsList.languageName);
const releaseYear = ref(filmsList.releaseYear);
        
const setNewLanguageName = function(name) {
    languageName.value = name;
};

const setNewReleaseYear = function(year) {
    releaseYear.value = year;
};

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

// События Broadcast передаются в массиве
const pusherEvents = ['AddFilmInUserList'];

const onBeforeForAddFilm = () => {
            app.isRequest = true;
        };

const onErrorForAddFilm = errors => {
            app.errorRequest(errors);
        };

const onFinishForAddFilm = () => {
            app.isRequest = false;
        };

/**
 * Добавляет фильм в коллекцию пользователя
 * @param {Element} tag
 * @returns {undefined}
 */
const addFilm = function(tag) {
    // Если на странице выполняется запрос, выходим из функции
    if(app.isRequest) {
        return;
    }
    
    const td = tag.closest('td');
    // Защита от повторного клика
    td.classList.remove('add-film');
    
    filmId.value = td.getAttribute('data-film_id');
    
    router.post(filmsList.getUrl(`userfilms/addfilm/${filmId.value}`), {}, {
            preserveScroll: true,
            onBefore: onBeforeForAddFilm,
            onError: onErrorForAddFilm,
            onFinish: onFinishForAddFilm
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
    setTimeout(function() {
        e.target.setAttribute('disabled', '');
        filmsList.refreshFilms(languageName.value, releaseYear.value, '/films');
    }, 2000);
};

watch([languageName, releaseYear], () => {
    filmsList.refreshFilms(languageName.value, releaseYear.value, '/films');
});
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
                    <th>Год выхода</th>
                    <th></th>
                </tr>
                <tr>
                    <th></th>
                    <th><input type="text" v-model="filmsList.title" @keyup.once="putFilms"></th>
                    <th><input type="text" v-model="filmsList.description" @keyup="putFilms"></th>
                    <th><LangugeDropdown :languageName="languageName" :setNewLanguageName="setNewLanguageName"/></th>
                    <th><YearDropdown :releaseYear="releaseYear" :setNewReleaseYear="setNewReleaseYear"/></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(film, index) in films.data" class="hover:bg-green-300">
                    <td>{{ films.from + index }}</td>
                    <td>{{ film.title }}</td>
                    <td>{{ film.description }}</td>
                    <td class="text-center">{{ film.languageName }}</td>
                    <td class="text-center">{{ film.releaseYear }}</td>
                    <td
                        :class="film.isAvailable ? null : 'add-film'"
                        :data-film_id="film.isAvailable ? null : film.id"
                    >
                        <Spinner styleSpinner="h-4 text-orange-200 fill-orange-700" class="flex justify-center" v-if="app.isRequest && filmId == film.id" />
                        <template v-else>
                            <CheckCircleSvg v-if="film.isAvailable" title="Данный фильм уже в вашей коллекции" />
                            <PlusCircleSvg v-else title="Добавить фильм в коллекцию" />
                        </template>
                    </td>
                </tr>
            </tbody>
        </table>
        
        <Buttons :links="films.links" v-if="films.total > 0" />
    
        <EchoAuth :user="user" :events="pusherEvents" />
    </AuthLayout>
</template>
