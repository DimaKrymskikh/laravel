import '@/bootstrap';
import { setActivePinia, createPinia } from 'pinia';

import { useAppStore } from '@/Stores/app';
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

describe("@/Stores/app", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    
    it("handlerStart и handlerFinish изменяют isGlobalRequest", () => {
        const app = useAppStore();
        expect(app.isGlobalRequest).toBe(false);
        
        app.handlerStart();
        expect(app.isGlobalRequest).toBe(true);
        
        app.handlerFinish();
        expect(app.isGlobalRequest).toBe(false);
    });
    
    it("axios получает правильные параметры при вызове app.request", async () => {
        const app = useAppStore();
        
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
        const app = useAppStore();
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
        const app = useAppStore();
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
        const app = useAppStore();
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
        const app = useAppStore();
        
        app.errorRequest(dataWithoutError.data);
        expect(app.isShowForbiddenModal).toBe(false);
        expect(app.errorMessage).toBe('');
        
        app.errorRequest(dataWithError.data);
        expect(app.isShowForbiddenModal).toBe(true);
        expect(app.errorMessage).toBe('Ошибка');
    });
});
