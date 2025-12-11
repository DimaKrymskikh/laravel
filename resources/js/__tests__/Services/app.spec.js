import '@/bootstrap';

import { defineComponent } from 'vue';
import { mount } from "@vue/test-utils";
import { app, modal, useCountdown } from '@/Services/app';
import { cities_user } from '@/__tests__/data/cities';

vi.mock('axios');

const dataWithoutError = {
    data: {
        cities: cities_user,
        errors: {}
    }
};

const dataWithError = {
    data: {
        message: 'Ошибка'
    }
};

const TestComponent = defineComponent({
    template: '<div></div>',
    setup () {
        return {
            // 4000 секунд: 1 час, 6 минут, 40 секунд
            countdown: useCountdown(4000)
        };
    }
});

describe("@/Services/app", () => {
    // В начале каждого теста устанавливаем дефолтные значения
    beforeEach(() => {
        vi.useFakeTimers();
        
        app.isRequest = false;
        app.isShowForbiddenModal = false;
        app.errorMessage = '';
    });
    
    afterEach(() => {
        vi.useRealTimers();
    });
    
    it("axios получает правильные параметры при вызове app.request", async () => {
        await app.request('/films', 'GET');
        expect(axios).toHaveBeenCalledTimes(1);
        expect(axios).toHaveBeenCalledWith('/films', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json'
                },
                data: null
        });
        
        await app.request('/films', 'POST', {title: 'Title'});
        expect(axios).toHaveBeenCalledTimes(2);
        expect(axios).toHaveBeenCalledWith('/films', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                data: {title: 'Title'}
        });
    });
    
    it("axios возвращает ответ с данными errors = {}", async () => {
        axios.mockResolvedValueOnce(dataWithoutError);
        
        const result = await app.request('/films', 'GET');
        expect(axios).toHaveBeenCalledTimes(1);
        expect(result).toEqual(dataWithoutError.data);
        // Модальное окно для ошибки не появляется
        expect(app.isShowForbiddenModal).toBe(false);
        // Сообщение об ошибке не изменилось (пустая строка)
        expect(app.errorMessage).toBe('');
    });
    
    it("axios возвращает ответ с данными message = 'Ошибка'", async () => {
        axios.mockResolvedValueOnce(dataWithError);
        
        const result = await app.request('/films', 'GET');
        expect(axios).toHaveBeenCalledTimes(1);
        expect(result).toEqual(dataWithError.data);
        // Появляется модальное окно с ошибкой
        expect(app.isShowForbiddenModal).toBe(true);
        // Сообщение об ошибке изменилось
        expect(app.errorMessage).toBe('Ошибка');
    });
    
    it("axios отклоняет ответ", async () => {
        axios.mockRejectedValueOnce(new Error('Фатальная ошибка'));
        
        const result = await app.request('/films', 'GET');
        expect(axios).toHaveBeenCalledTimes(1);
        // result = undefined
        expect(result).toBe(undefined);
        // Появляется модальное окно с ошибкой
        expect(app.isShowForbiddenModal).toBe(true);
        // Сообщение об ошибке изменилось
        expect(app.errorMessage).toBe('Фатальная ошибка');
    });
    
    it("errorRequest изменяет объект app", async () => {
        app.errorRequest(dataWithoutError.data);
        expect(app.isShowForbiddenModal).toBe(false);
        expect(app.errorMessage).toBe('');
        
        app.errorRequest(dataWithError.data);
        expect(app.isShowForbiddenModal).toBe(true);
        expect(app.errorMessage).toBe('Ошибка');
    });
    
    it("modal.show()", () => {
        modal.isShow = false;
        modal.show();
        expect(modal.isShow).toBe(true);
    });
    
    it("modal.hide()", () => {
        modal.isShow = true;
        modal.hide();
        expect(modal.isShow).toBe(false);
    });
    
    it("useCountdown: startTimer", () => {
        const wrapper = mount(TestComponent, {});
        // При первом монтировании TestComponent почему-то запускается некий таймер, нужно 3 секунды, чтобы он обнулился
        vi.advanceTimersByTime(3000);
        expect(vi.getTimerCount()).toBe(0);
        
        // Запускаем таймер 
        wrapper.vm.countdown.startTimer();
        expect(vi.getTimerCount()).toBe(1);
        // Повторный запуск startTimer() не создаёт новый таймер
        wrapper.vm.countdown.startTimer();
        expect(vi.getTimerCount()).toBe(1);
        
        expect(wrapper.vm.countdown.timeInSeconds.value).toBe(4000);
        expect(wrapper.vm.countdown.formattedTime.value).toBe('1 ч. 06 м. 40 с.');
        
        vi.advanceTimersByTime(40 * 1000);
        expect(wrapper.vm.countdown.timeInSeconds.value).toBe(3960);
        expect(wrapper.vm.countdown.formattedTime.value).toBe('1 ч. 06 м. 00 с.');
        
        vi.advanceTimersByTime(6 * 60 * 1000);
        expect(wrapper.vm.countdown.timeInSeconds.value).toBe(3600);
        expect(wrapper.vm.countdown.formattedTime.value).toBe('1 ч. 00 м. 00 с.');
        
        vi.advanceTimersByTime(1000);
        expect(wrapper.vm.countdown.timeInSeconds.value).toBe(3599);
        expect(wrapper.vm.countdown.formattedTime.value).toBe('0 ч. 59 м. 59 с.');
        
        vi.advanceTimersByTime((59 * 60 + 59) * 1000);
        expect(wrapper.vm.countdown.timeInSeconds.value).toBe(0);
        expect(wrapper.vm.countdown.formattedTime.value).toBe('время истекло');
        expect(vi.getTimerCount()).toBe(1);
        // В следующую секунду таймер обнуляется
        vi.advanceTimersByTime(1000);
        expect(wrapper.vm.countdown.timeInSeconds.value).toBe(0);
        expect(wrapper.vm.countdown.formattedTime.value).toBe('время истекло');
        expect(vi.getTimerCount()).toBe(0);
    });
    
    it("useCountdown: onUnmounted", () => {
        expect(vi.getTimerCount()).toBe(0);
        const wrapper = mount(TestComponent, {});
        expect(vi.getTimerCount()).toBe(0);
        
        wrapper.vm.countdown.startTimer();
        expect(vi.getTimerCount()).toBe(1);
        
        wrapper.unmount();
        expect(vi.getTimerCount()).toBe(0);
    });
});
