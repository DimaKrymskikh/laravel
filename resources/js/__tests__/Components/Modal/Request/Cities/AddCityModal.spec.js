import { mount } from "@vue/test-utils";

import { router } from '@inertiajs/vue3';

import { app } from '@/Services/app';
import { city } from '@/Services/Content/cities';
import AddCityModal from '@/Components/Modal/Request/Cities/AddCityModal.vue';

import { checkBaseModal } from '@/__tests__/methods/checkBaseModal';
import { checkInputField } from '@/__tests__/methods/checkInputField';
import { eventCurrentTargetClassListContainsFalse } from '@/__tests__/fake/Event';

vi.mock('@inertiajs/vue3');
        
const hideModal = vi.spyOn(city, 'hideAddCityModal');

const getWrapper = function() {
    return mount(AddCityModal);
};

const checkContent = function(wrapper) {
    // Проверка равенства переменных ref начальным данным
    expect(wrapper.vm.cityName).toBe(city.name);
    expect(wrapper.vm.openWeatherId).toBe(city.openWeatherId);
    expect(wrapper.vm.errorsName).toBe('');
    expect(wrapper.vm.errorsOpenWeatherId).toBe('');

    // Заголовок модального окна задаётся
    expect(wrapper.text()).toContain('Добавление города');
};
        
describe("@/Components/Modal/Request/AddCityModal.vue", () => {
    it("Монтирование компоненты AddCityModal (isRequest: false)", async () => {
        const wrapper = getWrapper();
        
        checkContent (wrapper);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 2);
        checkInputField.checkPropsInputField(inputFields[0], 'Имя города:', 'text', wrapper.vm.errorsName, wrapper.vm.cityName, true);
        checkInputField.checkPropsInputField(inputFields[1], 'Id города в OpenWeather:', 'text', wrapper.vm.errorsOpenWeatherId, wrapper.vm.openWeatherId);
        
        checkInputField.checkInputFieldWhenThereIsNoRequest(inputFields[0], wrapper.vm.cityName, 'Имя города');
        checkInputField.checkInputFieldWhenThereIsNoRequest(inputFields[1], wrapper.vm.openWeatherId, '7777');
        
        await checkBaseModal.hideBaseModal(wrapper, hideModal);
        await checkBaseModal.submitRequestInBaseModal(wrapper, router.post);
    });
    
    it("Монтирование компоненты AddCityModal (isRequest: true)", async () => {
        app.isRequest = true;
        const wrapper = getWrapper();
        
        checkContent (wrapper);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 2);
        checkInputField.checkPropsInputField(inputFields[0], 'Имя города:', 'text', wrapper.vm.errorsName, wrapper.vm.cityName, true);
        checkInputField.checkPropsInputField(inputFields[1], 'Id города в OpenWeather:', 'text', wrapper.vm.errorsOpenWeatherId, wrapper.vm.openWeatherId);
        
        checkInputField.checkInputFieldWhenRequestIsMade(inputFields[0], wrapper.vm.cityName, 'Имя города');
        checkInputField.checkInputFieldWhenRequestIsMade(inputFields[1], wrapper.vm.openWeatherId, '7777');
        
        await checkBaseModal.notHideBaseModal(wrapper, hideModal);
        await checkBaseModal.notSubmitRequestInBaseModal(wrapper, router.post);
    });
    
    it("Функция handlerAddCity вызывает router.post с нужными параметрами", () => {
        const wrapper = getWrapper();
        
        const options = {
            onBefore: expect.anything(),
            onSuccess: expect.anything(),
            onError: expect.anything(),
            onFinish: expect.anything()
        };

        wrapper.vm.handlerAddCity(eventCurrentTargetClassListContainsFalse);
        
        expect(router.post).toHaveBeenCalledTimes(1);
        expect(router.post).toHaveBeenCalledWith('/admin/cities', {
                name: wrapper.vm.cityName,
                open_weather_id: wrapper.vm.openWeatherId
            }, options);
    });
    
    it("Проверка функции onBeforeForHandlerAddCity", () => {
        const wrapper = getWrapper();
        
        wrapper.vm.errorsName = 'ErrorName';
        wrapper.vm.errorsOpenWeatherId = 'ErrorOpenWeatherId';
        wrapper.vm.onBeforeForHandlerAddCity();
        
        expect(app.isRequest).toBe(true);
        expect(wrapper.vm.errorsName).toBe('');
        expect(wrapper.vm.errorsOpenWeatherId).toBe('');
    });
    
    it("Проверка функции onSuccessForHandlerAddCity", async () => {
        const wrapper = getWrapper();
        
        expect(hideModal).not.toHaveBeenCalled();
        wrapper.vm.onSuccessForHandlerAddCity();
        
        expect(hideModal).toHaveBeenCalledTimes(1);
        expect(hideModal).toHaveBeenCalledWith();
    });
    
    it("Проверка функции onErrorForHandlerAddCity", async () => {
        const wrapper = getWrapper();
        
        expect(wrapper.vm.errorsName).toBe('');
        expect(wrapper.vm.errorsOpenWeatherId).toBe('');
        wrapper.vm.onErrorForHandlerAddCity({ name: 'ErrorName', open_weather_id: 'ErrorOpenWeatherId' });
        
        expect(wrapper.vm.errorsName).toBe('ErrorName');
        expect(wrapper.vm.errorsOpenWeatherId).toBe('ErrorOpenWeatherId');
    });
    
    it("Проверка функции onFinishForHandlerAddCity", async () => {
        app.isRequest = true;
        const wrapper = getWrapper();
        
        wrapper.vm.onFinishForHandlerAddCity();
        expect(app.isRequest).toBe(false);
    });
});
