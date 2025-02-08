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
        
    const setOptions = function(actors) {
        page.value = actors.current_page;
        perPage.value = actors.per_page;

        const urlParams = new URLSearchParams(window.location.search);
        name.value = !!urlParams.get('name') ? urlParams.get('name') : '';
    };

    return {
        name,
        page,
        perPage,
        getUrl,
        setOptions
    };
});
