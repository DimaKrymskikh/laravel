import { ref } from 'vue';
import { defineStore } from "pinia";

const filmsModel = function() {
    return function() {
        // Сортировка по названию фильмов
        const title = ref('');
        // Сортировка по описанию фильмов
        const description = ref('');
        // Активная страница
        const page = ref(1);
        // Число элементов на странице
        const perPage = ref(20);

        const getUrl = function(url) {
            return `${url}?page=${page.value}&number=${perPage.value}&title=${title.value}&description=${description.value}`;
        };

        return {
            title,
            description,
            page,
            perPage,
            getUrl
        };
    };
};

export const useFilmsListStore = defineStore("filmsList", filmsModel());
export const useFilmsAccountStore = defineStore("filmsAccount", filmsModel());
