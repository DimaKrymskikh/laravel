import { ref } from 'vue';
import { defineStore } from "pinia";

export const useActorsListStore = defineStore("actorsList", function() {
        // Сортировка по полному имени актёра
        const name = ref('');
        // Активная страница
        const page = ref(1);
        // Число элементов на странице
        const perPage = ref(20);

        const getUrl = function(id) {
            const strGetParameters = `page=${page.value}&number=${perPage.value}&name=${name.value}`;
            
            return id ? `/admin/actors/${id}?${strGetParameters}` : `/admin/actors?${strGetParameters}`;
        };

        return {
            name,
            page,
            perPage,
            getUrl
        };
    });
