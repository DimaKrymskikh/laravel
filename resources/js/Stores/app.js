import { ref } from 'vue';
import { defineStore } from "pinia";

export const useAppStore = defineStore("app", () => {
    const isRequest = ref(false);
    const isShowForbiddenModal = ref(false);
    const errorMessage = ref('');
    
    async function request(url, method, data = null) {
        isRequest.value = true;
        
        let result;

        try {
            const response = await axios(url, {
                method,
                headers: {
                    'Content-Type': 'application/json'
                },
                data
            });

            result = await response.data;
        } catch(e) {
            errorMessage.value = e.message;
            isShowForbiddenModal.value = true;
        } finally {
            // При исключениях, например, OpenWeatherException laravel возвращает ответ с кодом 200
            errorRequest(result);
            isRequest.value = false;
            return result;
        }
    }
    
    function errorRequest(err) {
        if(err && err.message) {
            errorMessage.value = err.message;
            isShowForbiddenModal.value = true;
        }
    }
    
    return { 
        request, 
        errorRequest, 
        isRequest, 
        isShowForbiddenModal,
        errorMessage
    };
});
