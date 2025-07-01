import { mount } from "@vue/test-utils";

import { router } from '@inertiajs/vue3';

import { app } from '@/Services/app';
import { language } from '@/Services/Content/languages';
import AddLanguageModal from '@/Components/Modal/Request/Languages/AddLanguageModal.vue';

import { checkBaseModal } from '@/__tests__/methods/checkBaseModal';
import { checkInputField } from '@/__tests__/methods/checkInputField';
import { eventCurrentTargetClassListContainsFalse } from '@/__tests__/fake/Event';

vi.mock('@inertiajs/vue3');
        
const hideModal = vi.spyOn(language, 'hideAddLanguageModal');

const getWrapper = function() {
    return mount(AddLanguageModal);
};

const checkContent = function(wrapper) {
    // Проверка равенства переменных ref начальным данным
    expect(wrapper.vm.languageName).toBe(language.name);
    expect(wrapper.vm.errorsName).toBe('');

    // Заголовок модального окна задаётся
    expect(wrapper.text()).toContain('Добавление языка');
};
        
describe("@/Components/Modal/Request/Languages/AddLanguageModal.vue", () => {
    it("Монтирование компоненты AddLanguageModal (isRequest: false)", async () => {
        const wrapper = getWrapper();
        
        checkContent(wrapper);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 1);
        checkInputField.checkPropsInputField(inputFields[0], 'Имя языка:', 'text', wrapper.vm.errorsName, wrapper.vm.languageName, true);
        
        checkInputField.checkInputFieldWhenThereIsNoRequest(inputFields[0], wrapper.vm.languageName, 'Китайский');
        
        await checkBaseModal.hideBaseModal(wrapper, hideModal);
        await checkBaseModal.submitRequestInBaseModal(wrapper, router.post);
    });
    
    it("Монтирование компоненты AddLanguageModal (isRequest: true)", async () => {
        app.isRequest = true;
        
        const wrapper = getWrapper();
        
        checkContent (wrapper);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 1);
        checkInputField.checkPropsInputField(inputFields[0], 'Имя языка:', 'text', wrapper.vm.errorsName, wrapper.vm.languageName, true);
        
        checkInputField.checkInputFieldWhenRequestIsMade(inputFields[0], wrapper.vm.languageName, 'Китайский');
        
        await checkBaseModal.notHideBaseModal(wrapper, hideModal);
        await checkBaseModal.notSubmitRequestInBaseModal(wrapper, router.post);
    });
    
    it("Функция handlerAddLanguage вызывает router.post с нужными параметрами", () => {
        const options = {
            onBefore: expect.anything(),
            onSuccess: expect.anything(),
            onError: expect.anything(),
            onFinish: expect.anything()
        };
        
        const wrapper = getWrapper();
        
        wrapper.vm.handlerAddLanguage(eventCurrentTargetClassListContainsFalse);
        
        expect(router.post).toHaveBeenCalledTimes(1);
        expect(router.post).toHaveBeenCalledWith('/admin/languages', { name: language.name }, options);
    });
    
    it("Проверка функции onBeforeForHandlerAddLanguage", () => {
        const wrapper = getWrapper();
        wrapper.vm.errorsName = 'TestName';
        wrapper.vm.onBeforeForHandlerAddLanguage();
        
        expect(app.isRequest).toBe(true);
        expect(wrapper.vm.errorsName).toBe('');
    });
    
    it("Проверка функции onSuccessForHandlerAddLanguage", async () => {
        const wrapper = getWrapper();
        
        expect(hideModal).not.toHaveBeenCalled();
        wrapper.vm.onSuccessForHandlerAddLanguage();
        
        expect(hideModal).toHaveBeenCalledTimes(1);
        expect(hideModal).toHaveBeenCalledWith();
    });
    
    it("Проверка функции onErrorForHandlerAddLanguage", async () => {
        const wrapper = getWrapper();
        
        expect(wrapper.vm.errorsName).toBe('');
        wrapper.vm.onErrorForHandlerAddLanguage({name: 'ErrorName'});
        
        expect(wrapper.vm.errorsName).toBe('ErrorName');
    });
    
    it("Проверка функции onFinishForHandlerAddLanguage", async () => {
        app.isRequest = true;
        
        const wrapper = getWrapper();
        
        wrapper.vm.onFinishForHandlerAddLanguage();
        
        expect(app.isRequest).toBe(false);
    });
});
