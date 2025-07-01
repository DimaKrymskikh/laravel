import { mount } from "@vue/test-utils";
import { router } from '@inertiajs/vue3';

import { setActivePinia, createPinia } from 'pinia';
import { app } from '@/Services/app';
import { language } from '@/Services/Content/languages';
import RemoveLanguageModal from '@/Components/Modal/Request/Languages/RemoveLanguageModal.vue';

import { checkBaseModal } from '@/__tests__/methods/checkBaseModal';
import { checkInputField } from '@/__tests__/methods/checkInputField';
import { eventCurrentTargetClassListContainsFalse } from '@/__tests__/fake/Event';

vi.mock('@inertiajs/vue3');
        
const hideModal = vi.spyOn(language, 'hideRemoveLanguageModal');

const getWrapper = function() {
    return mount(RemoveLanguageModal);
};

const checkContent = function(wrapper) {
    // Проверка равенства переменных ref начальным данным
    expect(wrapper.vm.inputPassword).toBe('');
    expect(wrapper.vm.errorsPassword).toBe('');

    // Заголовок модального окна задаётся
    expect(wrapper.text()).toContain('Подтверждение удаления языка');
    // Содержится вопрос модального окна
    expect(wrapper.text()).toContain('Вы действительно хотите удалить  язык?');
};

describe("@/Components/Modal/Request/Languages/RemoveLanguageModal.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    
    it("Монтирование компоненты RemoveLanguageModal (isRequest: false)", async () => {
        const wrapper = getWrapper();
        
        checkContent(wrapper);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 1);
        checkInputField.checkPropsInputField(inputFields[0], 'Введите пароль:', 'password', wrapper.vm.errorsPassword, wrapper.vm.inputPassword, true);
        checkInputField.checkInputFieldWhenThereIsNoRequest(inputFields[0], wrapper.vm.inputPassword, 'TestPassword');
        
        await checkBaseModal.hideBaseModal(wrapper, hideModal);
        await checkBaseModal.submitRequestInBaseModal(wrapper, router.delete);
    });
    
    it("Монтирование компоненты RemoveLanguageModal (isRequest: true)", async () => {
        app.isRequest = true;
        const wrapper = getWrapper(true);
        
        checkContent(wrapper);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 1);
        checkInputField.checkPropsInputField(inputFields[0], 'Введите пароль:', 'password', wrapper.vm.errorsPassword, wrapper.vm.inputPassword, true);
        checkInputField.checkInputFieldWhenRequestIsMade(inputFields[0], wrapper.vm.inputPassword, 'TestPassword');
        
        await checkBaseModal.notHideBaseModal(wrapper, hideModal);
        await checkBaseModal.notSubmitRequestInBaseModal(wrapper, router.delete);
    });
    
    it("Функция handlerRemoveLanguage вызывает router.delete с нужными параметрами", () => {
        const wrapper = getWrapper();
        const options = {
            preserveScroll: true,
            data: {
                password: wrapper.vm.inputPassword
            },
            onBefore: expect.anything(),
            onSuccess: expect.anything(),
            onError: expect.anything(),
            onFinish: expect.anything()
        };
        
        wrapper.vm.handlerRemoveLanguage(eventCurrentTargetClassListContainsFalse);
        
        expect(router.delete).toHaveBeenCalledTimes(1);
        expect(router.delete).toHaveBeenCalledWith(`/admin/languages/${language.id}`, options);
    });
    
    it("Проверка функции onBeforeForHandlerRemoveLanguage", () => {
        const wrapper = getWrapper();
        wrapper.vm.errorsPassword = 'TestPassword';
        wrapper.vm.onBeforeForHandlerRemoveLanguage();
        
        expect(app.isRequest).toBe(true);
        expect(wrapper.vm.errorsPassword).toBe('');
    });
    
    it("Проверка функции onSuccessForHandlerRemoveLanguage", async () => {
        const wrapper = getWrapper();
        
        expect(hideModal).not.toHaveBeenCalled();
        wrapper.vm.onSuccessForHandlerRemoveLanguage();
        
        expect(hideModal).toHaveBeenCalledTimes(1);
        expect(hideModal).toHaveBeenCalledWith();
    });
    
    it("Проверка функции onErrorForHandlerRemoveLanguage ({password: 'ErrorPassword'})", async () => {
        const wrapper = getWrapper();
        
        expect(wrapper.vm.errorsPassword).toBe('');
        expect(app.isShowForbiddenModal).toBe(false);
        
        wrapper.vm.onErrorForHandlerRemoveLanguage({password: 'ErrorPassword'});
        
        expect(wrapper.vm.errorsPassword).toBe('ErrorPassword');
        expect(app.errorMessage).toBe('');
        expect(app.isShowForbiddenModal).toBe(false);
    });
    
    it("Проверка функции onErrorForHandlerRemoveLanguage ({ message: 'ServerError' })", async () => {
        const wrapper = getWrapper();
        
        expect(wrapper.vm.errorsPassword).toBe('');
        expect(app.isShowForbiddenModal).toBe(false);
        
        wrapper.vm.onErrorForHandlerRemoveLanguage({ message: 'ServerError' });
        
        expect(wrapper.vm.errorsPassword).toBe('');
        expect(app.errorMessage).toBe('ServerError');
        expect(app.isShowForbiddenModal).toBe(true);
    });
    
    it("Проверка функции onFinishForHandlerRemoveLanguage", async () => {
        app.isRequest = true;
        const wrapper = getWrapper();
        
        wrapper.vm.onFinishForHandlerRemoveLanguage();
        expect(app.isRequest).toBe(false);
    });
});
