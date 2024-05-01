import { ref } from 'vue';
import { defineStore } from "pinia";

const weatherModel = function() {
    return function() {
        // Активная страница
        const page = ref(1);
        // Число элементов на странице
        const perPage = ref(20);

        const getUrl = function(url) {
            return `${url}?page=${page.value}&number=${perPage.value}`;
        };

        return {
            page,
            perPage,
            getUrl
        };
    };
};

export const useWeatherPageAuthStore = defineStore("weatherPageAuth", weatherModel());
