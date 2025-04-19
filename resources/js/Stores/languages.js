import { ref } from 'vue';
import { defineStore } from "pinia";

export const useLanguagesListStore = defineStore("languagesList", function() {
    const languages = ref([]);
    
    return {
        languages
    };
});
