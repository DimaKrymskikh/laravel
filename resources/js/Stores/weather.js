import { ref } from 'vue';
import { defineStore } from "pinia";
import { getPaginationOptions } from "@/Services/pagination.js";

const weatherModel = function() {
    return function() {
        const { page, perPage } = getPaginationOptions();
    
        const datefrom = ref('');
        const dateto = ref('');

        const getUrl = function(url) {
            return `${url}?page=${page.value}&number=${perPage.value}&datefrom=${datefrom.value}&dateto=${dateto.value}`;
        };

        return {
            page,
            perPage,
            getUrl,
            datefrom,
            dateto
        };
    };
};

export const useWeatherPageAuthStore = defineStore("weatherPageAuth", weatherModel());
