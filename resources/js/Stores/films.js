import { ref } from 'vue';
import { defineStore } from "pinia";

const filmsModel = function() {
    return function() {
        // Сортировка по названию фильмов
        const title = ref('');
        // Сортировка по описанию фильмов
        const description = ref('');
        // Год выхода фильма
        const release_year = ref('');
        // Активная страница
        const page = ref(1);
        // Число элементов на странице
        const perPage = ref(20);

        const getUrl = function(url) {
            return `${url}?page=${page.value}&number=${perPage.value}&title_filter=${title.value}&description_filter=${description.value}&release_year_filter=${release_year.value}`;
        };
        
        const resetSearchFilter = function() {
            title.value = '';
            description.value = '';
            release_year.value = '';
        };

        return {
            title,
            description,
            release_year,
            page,
            perPage,
            getUrl,
            resetSearchFilter
        };
    };
};

export const useFilmsListStore = defineStore("filmsList", filmsModel());
export const useFilmsAccountStore = defineStore("filmsAccount", filmsModel());
export const useFilmsAdminStore = defineStore("filmsAdmin", filmsModel());
