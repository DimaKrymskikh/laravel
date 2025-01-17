import { defineStore } from "pinia";

export const useGlobalConstsStore = defineStore("globalConsts", () => {
    // Массив должен совпадать с Paginator::PAGINATOR_PER_PAGE_LIST
    const paginatorPerPageList = [10, 20, 50, 100, 1000];
    
    return {
        paginatorPerPageList
    };
});
