<script setup>
import { inject, reactive, ref, watch } from 'vue';
import { Head } from '@inertiajs/vue3'
import { router } from '@inertiajs/vue3';
import { film } from '@/Services/Content/films';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AddFilmBlock from '@/Components/Pages/Admin/Films/AddFilmBlock.vue';
import RemoveFilmModal from '@/Components/Modal/Request/Films/RemoveFilmModal.vue';
import UpdateFilmModal from '@/Components/Modal/Request/Films/UpdateFilmModal.vue';
import UpdateFilmActorsBlock from '@/Components/Pages/Admin/Films/UpdateFilmActorsBlock.vue';
import UpdateFilmLanguageModal from '@/Components/Modal/Request/Films/UpdateFilmLanguageModal.vue';
import BreadCrumb from '@/Components/Elements/BreadCrumb.vue';
import LangugeDropdown from '@/Components/Elements/Dropdowns/LanguageDropdown.vue';
import YearDropdown from '@/Components/Elements/Dropdowns/YearDropdown.vue';
import Dropdown from '@/Components/Elements/Dropdown.vue';
import Buttons from '@/Components/Pagination/Buttons.vue';
import Info from '@/Components/Pagination/Info.vue';
import PencilSvg from '@/Components/Svg/PencilSvg.vue';
import TrashSvg from '@/Components/Svg/TrashSvg.vue';

const { films } = defineProps({
    films: Object,
    errors: Object
});

const titlePage = 'Фильмы';

// Список для хлебных крошек
const linksList = [{
            link: '/admin',
            text: 'Страница админа'
        }, {
            text: titlePage
        }];

const filmsAdmin = inject('filmsAdmin');
filmsAdmin.setOptions(films);

const languageName = ref(filmsAdmin.languageName);
const releaseYear = ref(filmsAdmin.releaseYear);
        
const setNewLanguageName = function(name) {
    languageName.value = name;
};

const setNewReleaseYear = function(year) {
    releaseYear.value = year;
};

// Изменяет число фильмов на странице
const changeNumberOfFilmsOnPage = function(newNumber) {
    filmsAdmin.page = 1;
    filmsAdmin.perPage = newNumber;
    router.get(filmsAdmin.getUrl('/admin/films'));
};

const putFilms = function(e) {
    setTimeout(function() {
        e.target.setAttribute('disabled', '');
        filmsAdmin.refreshFilms(languageName.value, releaseYear.value, '/admin/films');
    }, 2000);
};

const handlerTableChange = function(e) {
    let td = e.target.closest('td');
    
    if (td && td.classList.contains('update-film-field')) {
        film.id = td.getAttribute('data-film_id');
        film.title = td.getAttribute('data-film_title');
        film.fieldValue = td.getAttribute('data-field_value');
        film.field = td.getAttribute('data-field');
        film.showUpdateFilmModal();
    }
    
    if (td && td.classList.contains('update-film-actors')) {
        film.id = td.getAttribute('data-film_id');
        film.title = td.getAttribute('data-film_title');
        film.showUpdateFilmActorsModal();
    }
    
    if (td && td.classList.contains('update-film-language')) {
        film.id = td.getAttribute('data-film_id');
        film.title = td.getAttribute('data-film_title');
        film.showUpdateFilmLanguageModal();
    }
    
    if (td && td.classList.contains('remove-film')) {
        film.id = td.getAttribute('data-film_id');
        film.title = td.getAttribute('data-film_title');
        film.showRemoveFilmModal();
    }
};

watch([languageName, releaseYear], () => {
    filmsAdmin.refreshFilms(languageName.value, releaseYear.value, '/admin/films');
});
</script>

<template>
    <Head :title="titlePage" />
    <AdminLayout>
        <BreadCrumb :linksList="linksList" />
        <h1>{{ titlePage }}</h1>
        
        <div class="flex justify-between pb-4">
            <Dropdown
                buttonName="Число фильмов на странице"
                :itemsNumberOnPage="films.per_page"
                :changeNumber="changeNumberOfFilmsOnPage"
            />
            <AddFilmBlock />
        </div>
        
        <table class="container" @click="handlerTableChange">
            <caption>
                <Info :films='films' v-if="films.total > 0" />
            </caption>
            <thead>
                <tr>
                    <th>#</th>
                    <th colspan="2">Название</th>
                    <th colspan="2">Описание</th>
                    <th colspan="2">Актёры</th>
                    <th colspan="2">Язык</th>
                    <th colspan="2">Год выхода</th>
                    <th></th>
                </tr>
                <tr>
                    <th></th>
                    <th colspan="2"><input type="text" v-model="filmsAdmin.title" @keyup.once="putFilms"></th>
                    <th colspan="2"><input type="text" v-model="filmsAdmin.description" @keyup.once="putFilms"></th>
                    <th colspan="2"></th>
                    <th colspan="2"><LangugeDropdown :languageName="languageName" :setNewLanguageName="setNewLanguageName"/></th>
                    <th colspan="2"><YearDropdown :releaseYear="releaseYear" :setNewReleaseYear="setNewReleaseYear"/></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(film, index) in films.data">
                    <td>{{ films.from + index }}</td>
                    <td>{{ film.title }}</td>
                    <td class="update-film-field" data-field="title" :data-film_id="film.id" :data-film_title="film.title" :data-field_value="film.title">
                        <PencilSvg title="Изменить название фильма" />
                    </td>
                    <td>{{ film.description }}</td>
                    <td class="update-film-field" data-field="description" :data-film_id="film.id" :data-film_title="film.title" :data-field_value="film.description">
                        <PencilSvg title="Изменить описание фильма" />
                    </td>
                    <td>{{ film.actorsList }}</td>
                    <td
                        class="update-film-actors"
                        :data-film_id="film.id"
                        :data-film_title="film.title"
                    >
                        <PencilSvg title="Изменить список актёров" />
                    </td>
                    <td class="text-center">{{ film.languageName }}</td>
                    <td class="update-film-language" :data-film_id="film.id" :data-film_title="film.title">
                        <PencilSvg title="Изменить язык фильма" />
                    </td>
                    <td class="text-center">{{ film.releaseYear }}</td>
                    <td
                        class="update-film-field"
                        data-field="release_year"
                        :data-film_id="film.id"
                        :data-film_title="film.title"
                        :data-field_value="film.release_year"
                    >
                        <PencilSvg title="Изменить год выхода фильма" />
                    </td>
                    <td class="remove-film" :data-film_id="film.id" :data-film_title="film.title">
                        <TrashSvg title="Удалить фильм" />
                    </td>
                </tr>
            </tbody>
        </table>
        <div v-if="!films.total">
            Ни один фильм не найден, или ещё ни один фильм не добавлен.
        </div>
        
        <Buttons :links="films.links" v-if="films.total > 0" />
        
        <UpdateFilmModal
            v-if="film.isShowUpdateFilmModal"
        />
        
        <UpdateFilmActorsBlock />
        
        <UpdateFilmLanguageModal
            v-if="film.isShowUpdateFilmLanguageModal"
        />
        
        <RemoveFilmModal
            v-if="film.isShowRemoveFilmModal"
        />
    </AdminLayout>
</template>
