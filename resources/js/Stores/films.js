import { ref } from 'vue';
import { defineStore } from "pinia";
import { getPaginationOptions } from "@/Services/pagination.js";

const filmsModel = function() {
    return function() {
        const { page, perPage } = getPaginationOptions();
    
        // Сортировка по названию фильмов
        const title = ref('');
        // Сортировка по описанию фильмов
        const description = ref('');
        // Год выхода фильма
        const release_year = ref('');

        const getUrl = function(url) {
            return `${url}?page=${page.value}&number=${perPage.value}&` +
                    `title_filter=${title.value}&description_filter=${description.value}&release_year_filter=${release_year.value}`;
        };
        
        const resetSearchFilter = function() {
            title.value = '';
            description.value = '';
            release_year.value = '';
        };
        
        const setOptions = function(films) {
            page.value = films.current_page;
            perPage.value = films.per_page;
            
            const urlParams = new URLSearchParams(window.location.search);
            title.value = !!urlParams.get('title_filter') ? urlParams.get('title_filter') : '';
            description.value = !!urlParams.get('description_filter') ? urlParams.get('description_filter') : '';
            release_year.value = !!urlParams.get('release_year_filter') ? urlParams.get('release_year_filter') : '';
        };

        return {
            title,
            description,
            release_year,
            page,
            perPage,
            getUrl,
            resetSearchFilter,
            setOptions
        };
    };
};

export const useFilmsListStore = defineStore("filmsList", filmsModel());
export const useFilmsAccountStore = defineStore("filmsAccount", filmsModel());
export const useFilmsAdminStore = defineStore("filmsAdmin", filmsModel());
