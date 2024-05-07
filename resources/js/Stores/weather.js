import { ref } from 'vue';
import { defineStore } from "pinia";

const weatherModel = function() {
    return function() {
        // Активная страница
        const page = ref(1);
        // Число элементов на странице
        const perPage = ref(20);
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
