<script setup>
import { ref, inject, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AccountLayout from '@/Layouts/Auth/AccountLayout.vue';
import AlertPrimary from '@/Components/Alerts/AlertPrimary.vue';
import LangugeDropdown from '@/Components/Elements/Dropdowns/LanguageDropdown.vue';
import YearDropdown from '@/Components/Elements/Dropdowns/YearDropdown.vue';
import Dropdown from '@/Components/Elements/Dropdown.vue';
import Buttons from '@/Components/Pagination/Buttons.vue';
import Info from '@/Components/Pagination/Info.vue';
import EyeSvg from '@/Components/Svg/EyeSvg.vue';
import TrashSvg from '@/Components/Svg/TrashSvg.vue';
import FilmRemoveModal from '@/Components/Modal/Request/FilmRemoveModal.vue';
import EchoAuth from '@/Components/Broadcast/EchoAuth.vue';

const { films } = defineProps({
    films: Object | null,
    user: Object | null,
    errors: Object | null
});

const titlePage = 'ЛК: фильмы';

// Список для хлебных крошек
const linksList = [{
            link: '/',
            text: 'Главная страница'
        }, {
            text: titlePage
        }];

// События Broadcast передаются в массиве
const pusherEvents = ['RemoveFilmFromUserList'];

const filmsAccount = inject('filmsAccount');
filmsAccount.setOptions(films);

const languageName = ref(filmsAccount.languageName);
const releaseYear = ref(filmsAccount.releaseYear);
        
const setNewLanguageName = function(name) {
    languageName.value = name;
};

const setNewReleaseYear = function(year) {
    releaseYear.value = year;
};

// Отслеживает отрисовку/удаление модального окна для удаления фильма
const isShowFilmRemoveModal = ref(false);
// id удаляемого фильма
const removeFilmId = ref('');
// Название удаляемого фильма
const removeFilmTitle = ref('');

/**
 * Управляет изменениями в таблице фильмов
 * @param {Event} e
 * @returns {undefined}
 */
const handlerTableChange = function(e) {
    // Показывает модальное окно для удаления фильма
    // Находятся id и название удаляемого фильма
    if (e.target.closest('td') && e.target.closest('td').classList.contains('remove-film')) {
        removeFilmId.value = e.target.closest('td').getAttribute('data-film_id');
        removeFilmTitle.value = e.target.closest('td').getAttribute('data-film_title');
        isShowFilmRemoveModal.value = true;
    }
};

/**
 * Скрывает модальное окно для удаления фильма
 * @returns {undefined}
 */
const hideFilmRemoveModal = function() {
    isShowFilmRemoveModal.value = false;
};

// Изменяет число фильмов на странице
const changeNumberOfFilmsOnPage = function(newNumber) {
    filmsAccount.page = 1;
    filmsAccount.perPage = newNumber;
    router.get(filmsAccount.getUrl('/userfilms'));
};

const putFilms = function(e) {
    setTimeout(function() {
        e.target.setAttribute('disabled', '');
        filmsAccount.refreshFilms(languageName.value, releaseYear.value, '/userfilms');
    }, 2000);
};

watch([languageName, releaseYear], () => {
    filmsAccount.refreshFilms(languageName.value, releaseYear.value, '/userfilms');
});
</script>

<template>
    <Head :title="titlePage" />
    <AccountLayout :errors="errors" :user="user" :linksList="linksList">

            <div class="flex justify-start pb-4">
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
                        <th></th>
                    </tr>
                    <tr>
                        <th></th>
                        <th><input type="text" v-model="filmsAccount.title" @keyup="putFilms"></th>
                        <th><input type="text" v-model="filmsAccount.description" @keyup="putFilms"></th>
                        <th><LangugeDropdown :languageName="languageName" :setNewLanguageName="setNewLanguageName"/></th>
                        <th><YearDropdown :releaseYear="releaseYear" :setNewReleaseYear="setNewReleaseYear"/></th>
                        <th></th>
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
                        <td>
                            <Link :href="`/userfilms/${film.id}`">
                                <EyeSvg title="Посмотреть карточку фильма"/>
                            </Link>
                        </td>
                        <td class="remove-film" :data-film_id="film.id" :data-film_title="film.title">
                            <TrashSvg title="Удалить фильм из своей коллекции"/>
                        </td>
                    </tr>
                </tbody>
            </table>
        
            <AlertPrimary
                v-if="films.total == 0"
                class="text-center"
                text="В вашей коллекции нет фильмов"
            />

            <Buttons :links="films.links" v-if="films.total > 0" />
        
        <FilmRemoveModal
            :films="films"
            :removeFilmTitle="removeFilmTitle"
            :removeFilmId="removeFilmId"
            :hideFilmRemoveModal="hideFilmRemoveModal"
            v-if="isShowFilmRemoveModal"
        />
    
        <EchoAuth :user="user" :events="pusherEvents" />
    </AccountLayout>
</template>
