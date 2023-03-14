import { defineStore } from "pinia";

const filmsModel = function(url) {
    return function() {
        // Сортировка по названию фильмов
        const title = '';
        // Сортировка по описанию фильмов
        const description = '';
        // Активная страница
        const page = 1;
        // Число элементов на странице
        const perPage = 20;

        const getUrl = function() {
            return `${url}?page=${this.page}&number=${this.perPage}&title=${this.title}&description=${this.description}`;
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

export const filmsCatalogStore = defineStore("filmsCatalog", filmsModel('/catalog'));
export const filmsAccountStore = defineStore("filmsAccount", filmsModel('/account'));
