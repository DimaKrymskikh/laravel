import { mount } from "@vue/test-utils";

import { router } from '@inertiajs/vue3';

import { app } from '@/Services/app';
import { city } from '@/Services/Content/cities';
import RemoveCityModal from '@/Components/Modal/Request/Cities/RemoveCityModal.vue';

import { checkBaseModal } from '@/__tests__/methods/checkBaseModal';
import { checkInputField } from '@/__tests__/methods/checkInputField';
import { eventCurrentTargetClassListContainsFalse } from '@/__tests__/fake/Event';

vi.mock('@inertiajs/vue3');
        
const hideModal = vi.spyOn(city, 'hideRemoveCityModal');

const getWrapper = function() {
    return mount(RemoveCityModal);
};

const checkContent = function(wrapper) {
    // Проверка равенства переменных ref начальным данным
    expect(wrapper.vm.inputPassword).toBe('');
    expect(wrapper.vm.errorsPassword).toBe('');

    // Заголовок модального окна задаётся
    expect(wrapper.text()).toContain('Подтверждение удаления города');
    // Содержится вопрос модального окна с нужными параметрами
    expect(wrapper.text()).toContain('Вы действительно хотите удалить город');
    expect(wrapper.text()).toContain(city.name);
    expect(wrapper.text()).toContain(city.openWeatherId);
};

describe("@/Components/Modal/Request/RemoveCityModal.vue", () => {
    it("Монтирование компоненты RemoveCityModal (isRequest: false)", async () => {
        const wrapper = getWrapper();
        
        checkContent(wrapper);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 1);
        checkInputField.checkPropsInputField(inputFields[0], 'Введите пароль:', 'password', wrapper.vm.errorsPassword, wrapper.vm.inputPassword, true);
        checkInputField.checkInputFieldWhenThereIsNoRequest(inputFields[0], wrapper.vm.inputPassword, 'TestPassword');
        
        await checkBaseModal.hideBaseModal(wrapper, hideModal);
        await checkBaseModal.submitRequestInBaseModal(wrapper, router.delete);
    });
    
    it("Монтирование компоненты RemoveCityModal (isRequest: true)", async () => {
        app.isRequest = true;
        const wrapper = getWrapper();
        
        checkContent(wrapper);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 1);
        checkInputField.checkPropsInputField(inputFields[0], 'Введите пароль:', 'password', wrapper.vm.errorsPassword, wrapper.vm.inputPassword, true);
        checkInputField.checkInputFieldWhenRequestIsMade(inputFields[0], wrapper.vm.inputPassword, 'TestPassword');
        
        await checkBaseModal.notHideBaseModal(wrapper, hideModal);
        await checkBaseModal.notSubmitRequestInBaseModal(wrapper, router.delete);
    });
    
    it("Функция handlerRemoveCity вызывает router.delete с нужными параметрами", () => {
        const wrapper = getWrapper();
        
        wrapper.vm.handlerRemoveCity(eventCurrentTargetClassListContainsFalse);
        const options = {
            data: {
                password: wrapper.vm.inputPassword
            },
            preserveScroll: true,
            onBefore: expect.anything(),
            onSuccess: expect.anything(),
            onError: expect.anything(),
            onFinish: expect.anything()
        };
        
        expect(router.delete).toHaveBeenCalledTimes(1);
        expect(router.delete).toHaveBeenCalledWith(`cities/${city.id}`, options);
    });
    
    it("Проверка функции onBeforeForHandlerRemoveCity", () => {
        const wrapper = getWrapper();
        
        wrapper.vm.errorsPassword = 'ErrorPassword';
        wrapper.vm.onBeforeForHandlerRemoveCity();
        
        expect(app.isRequest).toBe(true);
        expect(wrapper.vm.errorsPassword).toBe('');
    });
    
    it("Проверка функции onSuccessForHandlerRemoveCity", async () => {
        const wrapper = getWrapper();
        
        expect(hideModal).not.toHaveBeenCalled();
        wrapper.vm.onSuccessForHandlerRemoveCity();
        
        expect(hideModal).toHaveBeenCalledTimes(1);
        expect(hideModal).toHaveBeenCalledWith();
    });
    
    it("Проверка функции onErrorForHandlerRemoveCity ({ password: 'ErrorPassword' })", async () => {
        const wrapper = getWrapper();
        
        expect(wrapper.vm.errorsPassword).toBe('');
        expect(wrapper.vm.app.isShowForbiddenModal).toBe(false);
        
        wrapper.vm.onErrorForHandlerRemoveCity({ password: 'ErrorPassword' });
        
        expect(wrapper.vm.errorsPassword).toBe('ErrorPassword');
        expect(app.errorMessage).toBe('');
        expect(app.isShowForbiddenModal).toBe(false);
    });
    
    it("Проверка функции onErrorForHandlerRemoveCity ({ message: 'ServerError' })", async () => {
        const wrapper = getWrapper();
        
        expect(wrapper.vm.errorsPassword).toBe('');
        expect(app.isShowForbiddenModal).toBe(false);
        
        wrapper.vm.onErrorForHandlerRemoveCity({ message: 'ServerError' });
        
        expect(wrapper.vm.errorsPassword).toBe('');
        expect(app.errorMessage).toBe('ServerError');
        expect(app.isShowForbiddenModal).toBe(true);
    });
    
    it("Проверка функции onFinishForHandlerRemoveCity", async () => {
        app.isRequest = true;
        const wrapper = getWrapper(true);
        
        wrapper.vm.onFinishForHandlerRemoveCity();
        expect(app.isRequest).toBe(false);
    });
});
