import { defineStore } from "pinia";

const paginationModel = function() {
    // Активная страница
    const page = 1;
    // Число элементов на странице
    const perPage = 20;

    const setData = function(page, perPage) {
        this.page = page;
        this.perPage = perPage;
    };
    
    return {
        page,
        perPage,
        setData
    };
};

export const paginationCatalogStore = defineStore("paginationCatalog", paginationModel);
export const paginationAccountStore = defineStore("paginationAccount", paginationModel);
