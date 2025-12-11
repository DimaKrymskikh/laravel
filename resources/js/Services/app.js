/**
 * Модуль, в котором содержатся объекты, повсеместно используемые в проекте
 */

import { ref, reactive, computed, onUnmounted } from 'vue';

/**
 * Основной объект для отправки запросов на сервер
 */
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

/**
 * Базовый объект для модальных окон
 */
export const modal = {
    isShow: false,
    show() {
        this.isShow = true;
    },
    hide() {
        this.isShow = false;
    }
};

/**
 * Управляет таймером отсчёта убывания времени
 * 
 * @param {int} seconds - начальное время в секундах
 * @returns {object}
 */
export const useCountdown = function(seconds) {
    const timeInSeconds = ref(seconds);
    let timerInterval = null;

    const formattedTime = computed(() => {
        const hours = Math.floor(timeInSeconds.value / 3600);
        const minutes = Math.floor(timeInSeconds.value / 60 - hours * 60);
        const seconds = timeInSeconds.value - hours * 3600 - minutes * 60;
        
        let timeString = undefined;
        if(timeInSeconds.value > 0) {
            timeString = `${hours ? hours.toString() : '0'} ч. ${minutes.toString().padStart(2, '0')} м. ${seconds.toString().padStart(2, '0')} с.`;
        } else {
            timeString = 'время истекло';
        }
        
        return timeString;
    });

    const startTimer = () => {
        if (timerInterval) {
            return;
        }
        
        timerInterval = setInterval(() => {
            if(timeInSeconds.value > 0) {
                timeInSeconds.value--;
            } else {
                stopTimer();
            }
        }, 1000);
    };
    
    const stopTimer = () => {
        clearInterval(timerInterval);
        timerInterval = null;
    };

    onUnmounted(() => {
        stopTimer();
    });
    
    return {
        formattedTime,
        startTimer,
        timeInSeconds
    };
};
