import { ref } from 'vue';
import { defineStore } from "pinia";
import { router } from '@inertiajs/vue3';
import { getPaginationOptions } from "@/Services/pagination.js";

function FilmsModel() {
    const { page, perPage } = getPaginationOptions();
    this.page = page;
    this.perPage = perPage;

    // Сортировка по названию фильмов
    this.title = ref('');
    // Сортировка по описанию фильмов
    this.description = ref('');
    // Год выхода фильма
    this.releaseYear = ref('');
    // Выбранный язык фильма
    this.languageName = ref('');
}

FilmsModel.prototype.getUrl = function(url) {
    return `${url}?page=${this.page}&number=${this.perPage}&` +
            `title_filter=${this.title}&description_filter=${this.description}&release_year_filter=${this.releaseYear}` +
            `&language_name_filter=${this.languageName}`;
};

FilmsModel.prototype.resetSearchFilter = function() {
    this.title = '';
    this.description = '';
    this.releaseYear = '';
    this.languageName = '';
};

FilmsModel.prototype.setOptions = function(films) {
    this.page = films.current_page;
    this.perPage = films.per_page;

    const urlParams = new URLSearchParams(window.location.search);
    this.title = !!urlParams.get('title_filter') ? urlParams.get('title_filter') : '';
    this.description = !!urlParams.get('description_filter') ? urlParams.get('description_filter') : '';
    this.releaseYear = !!urlParams.get('release_year_filter') ? urlParams.get('release_year_filter') : '';
    this.languageName = !!urlParams.get('language_name_filter') ? urlParams.get('language_name_filter') : '';
};

FilmsModel.prototype.refreshFilms = function(name, year, baseUrl) {
    this.page = 1;
    this.languageName = name;
    this.releaseYear = year;
    router.get(this.getUrl(baseUrl));
};

export const useFilmsListStore = defineStore("filmsList", () => new FilmsModel());
export const useFilmsAccountStore = defineStore("filmsAccount", () => new FilmsModel());
export const useFilmsAdminStore = defineStore("filmsAdmin", () => new FilmsModel());
