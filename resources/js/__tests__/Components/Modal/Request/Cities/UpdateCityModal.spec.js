import { mount } from "@vue/test-utils";

import { router } from '@inertiajs/vue3';

import { app } from '@/Services/app';
import { city } from '@/Services/Content/cities';
import UpdateCityModal from '@/Components/Modal/Request/Cities/UpdateCityModal.vue';

import { checkBaseModal } from '@/__tests__/methods/checkBaseModal';
import { checkInputField } from '@/__tests__/methods/checkInputField';
import { eventCurrentTargetClassListContainsFalse } from '@/__tests__/fake/Event';

vi.mock('@inertiajs/vue3');
        
const hideModal = vi.spyOn(city, 'hideUpdateCityModal');

const getWrapper = function(isRequest = false) {
    return mount(UpdateCityModal);
};

const checkContent = function(wrapper) {
    // Проверка равенства переменных ref начальным данным
    expect(wrapper.vm.cityName).toBe(city.name);
    expect(wrapper.vm.errorsName).toBe('');

    // Заголовок модального окна задаётся
    expect(wrapper.text()).toContain('Изменение названия города');
};

describe("@/Components/Modal/Request/UpdateCityModal.vue", () => {
    it("Монтирование компоненты UpdateCityModal (isRequest: false)", async () => {
        const wrapper = getWrapper();
        
        checkContent(wrapper);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 1);
        checkInputField.checkPropsInputField(inputFields[0], 'Имя города:', 'text', wrapper.vm.errorsName, wrapper.vm.cityName, true);
        checkInputField.checkInputFieldWhenThereIsNoRequest(inputFields[0], wrapper.vm.cityName, 'Имя нового города');
        
        await checkBaseModal.hideBaseModal(wrapper, hideModal);
        await checkBaseModal.submitRequestInBaseModal(wrapper, router.put);
    });
    
    it("Монтирование компоненты UpdateCityModal (isRequest: true)", async () => {
        app.isRequest = true;
        const wrapper = getWrapper();
        
        checkContent(wrapper);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 1);
        checkInputField.checkPropsInputField(inputFields[0], 'Имя города:', 'text', wrapper.vm.errorsName, wrapper.vm.cityName, true);
        checkInputField.checkInputFieldWhenRequestIsMade(inputFields[0], wrapper.vm.cityName, 'Имя нового города');
        
        await checkBaseModal.notHideBaseModal(wrapper, hideModal);
        await checkBaseModal.notSubmitRequestInBaseModal(wrapper, router.put);
    });
    
    it("Функция handlerUpdateCity вызывает router.put с нужными параметрами", () => {
        const wrapper = getWrapper();
        
        const options = {
            preserveScroll: true,
            onBefore: expect.anything(),
            onSuccess: expect.anything(),
            onError: expect.anything(),
            onFinish: expect.anything()
        };

        wrapper.vm.handlerUpdateCity(eventCurrentTargetClassListContainsFalse);
        
        expect(router.put).toHaveBeenCalledTimes(1);
        expect(router.put).toHaveBeenCalledWith(`cities/${city.id}`, { name: wrapper.vm.cityName }, options);
    });
    
    it("Проверка функции onBeforeForHandlerUpdateCity", () => {
        const wrapper = getWrapper();
        
        wrapper.vm.errorsName = 'ErrorName';
        wrapper.vm.onBeforeForHandlerUpdateCity();
        
        expect(app.isRequest).toBe(true);
        expect(wrapper.vm.errorsName).toBe('');
    });
    
    it("Проверка функции onSuccessForHandlerUpdateCity", async () => {
        const wrapper = getWrapper();
      
        expect(hideModal).not.toHaveBeenCalled();
        wrapper.vm.onSuccessForHandlerUpdateCity();
        
        expect(hideModal).toHaveBeenCalledTimes(1);
        expect(hideModal).toHaveBeenCalledWith();
    });
    
    it("Проверка функции onErrorForHandlerUpdateCity (валидация данных)", async () => {
        const wrapper = getWrapper();
        
        expect(wrapper.vm.errorsName).toBe('');
        wrapper.vm.onErrorForHandlerUpdateCity({name: 'ErrorName'});
        
        expect(wrapper.vm.errorsName).toBe('ErrorName');
    });
    
    it("Проверка функции onErrorForHandlerUpdateCity (ошибка сервера с message)", async () => {
        const wrapper = getWrapper();

        expect(hideModal).toHaveBeenCalledTimes(0);
        wrapper.vm.onErrorForHandlerUpdateCity({ message: 'В таблице thesaurus.cities нет записи с id=13' });
        expect(hideModal).toHaveBeenCalledTimes(1);
    });
    
    it("Проверка функции onFinishForHandlerUpdateCity", async () => {
        app.isRequest = true;
        const wrapper = getWrapper();
        
        wrapper.vm.onFinishForHandlerUpdateCity();
        expect(app.isRequest).toBe(false);
    });
});
