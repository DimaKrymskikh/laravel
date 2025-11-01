import { reactive } from 'vue';

export const app = reactive({
    isRequest: false,
    isShowForbiddenModal: false,
    errorMessage: '',
    
    async request(url, method, data = null) {
        this.isRequest = true;
        
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
            this.errorMessage = e.message;
            this.isShowForbiddenModal = true;
        } finally {
            // При исключениях, например, OpenWeatherException laravel возвращает ответ с кодом 200
            this.errorRequest(result);
            this.isRequest = false;
            
            return result;
        }
    },
    
    errorRequest(err) {
        if(err && err.message) {
            this.errorMessage = err.message;
            this.isShowForbiddenModal = true;
        }
    }
});

// Базовый объект для модальных окон
export const modal = {
    isShow: false,
    show() {
        this.isShow = true;
    },
    hide() {
        this.isShow = false;
    }
};
