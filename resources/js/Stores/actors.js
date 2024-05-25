import { ref } from 'vue';
import { defineStore } from "pinia";
import { getPaginationOptions } from "@/Services/pagination.js";

export const useActorsListStore = defineStore("actorsList", function() {
        const { page, perPage } = getPaginationOptions();
    
        // Сортировка по полному имени актёра
        const name = ref('');

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
