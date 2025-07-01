import { mount } from "@vue/test-utils";
import { router } from '@inertiajs/vue3';

import { app } from '@/Services/app';
import { language } from '@/Services/Content/languages';
import UpdateLanguageModal from '@/Components/Modal/Request/Languages/UpdateLanguageModal.vue';

import { checkBaseModal } from '@/__tests__/methods/checkBaseModal';
import { checkInputField } from '@/__tests__/methods/checkInputField';
import { eventCurrentTargetClassListContainsFalse } from '@/__tests__/fake/Event';

vi.mock('@inertiajs/vue3');
        
const hideModal = vi.spyOn(language, 'hideUpdateLanguageModal');

const getWrapper = function() {
    return mount(UpdateLanguageModal);
};

const checkContent = function(wrapper) {
    // Проверка равенства переменных ref начальным данным
    expect(wrapper.vm.languageName).toBe('');
    expect(wrapper.vm.errorsName).toBe('');

    // Заголовок модального окна задаётся
    expect(wrapper.text()).toContain('Изменение названия языка');
};

describe("@/Components/Modal/Request/Languages/UpdateLanguageModal.vue", () => {
    it("Монтирование компоненты UpdateLanguageModal (isRequest: false)", async () => {
        const wrapper = getWrapper();
        
        checkContent(wrapper);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 1);
        checkInputField.checkPropsInputField(inputFields[0], 'Имя языка:', 'text', wrapper.vm.errorsName, wrapper.vm.languageName, true);
        checkInputField.checkInputFieldWhenThereIsNoRequest(inputFields[0], wrapper.vm.languageName, 'Английский');
        
        await checkBaseModal.hideBaseModal(wrapper, hideModal);
        await checkBaseModal.submitRequestInBaseModal(wrapper, router.put);
    });
    
    it("Монтирование компоненты UpdateLanguageModal (isRequest: true)", async () => {
        app.isRequest = true;
        const wrapper = getWrapper(true);
        
        checkContent(wrapper);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 1);
        checkInputField.checkPropsInputField(inputFields[0], 'Имя языка:', 'text', wrapper.vm.errorsName, wrapper.vm.languageName, true);
        checkInputField.checkInputFieldWhenRequestIsMade(inputFields[0], wrapper.vm.languageName, 'Английский');
        
        await checkBaseModal.notHideBaseModal(wrapper, hideModal);
        await checkBaseModal.notSubmitRequestInBaseModal(wrapper, router.put);
    });
    
    it("Функция handlerUpdateLanguage вызывает router.put с нужными параметрами", () => {
        const options = {
            preserveScroll: true,
            onBefore: expect.anything(),
            onSuccess: expect.anything(),
            onError: expect.anything(),
            onFinish: expect.anything()
        };
        
        const wrapper = getWrapper();
        wrapper.vm.handlerUpdateLanguage(eventCurrentTargetClassListContainsFalse);
        
        expect(router.put).toHaveBeenCalledTimes(1);
        expect(router.put).toHaveBeenCalledWith(`/admin/languages/${language.id}`, { name: wrapper.vm.languageName }, options);
    });
    
    it("Проверка функции onBeforeForHandlerUpdateLanguage", () => {
        const wrapper = getWrapper();
        wrapper.vm.errorsName = 'TestName';
        wrapper.vm.onBeforeForHandlerUpdateLanguage();
        
        expect(app.isRequest).toBe(true);
        expect(wrapper.vm.errorsName).toBe('');
    });
    
    it("Проверка функции onSuccessForHandlerUpdateLanguage", async () => {
        const wrapper = getWrapper();
        
        expect(hideModal).not.toHaveBeenCalled();
        wrapper.vm.onSuccessForHandlerUpdateLanguage();
        
        expect(hideModal).toHaveBeenCalledTimes(1);
        expect(hideModal).toHaveBeenCalledWith();
    });
    
    it("Проверка функции onErrorForHandlerUpdateLanguage (валидация данных)", async () => {
        const wrapper = getWrapper();
        
        expect(wrapper.vm.errorsName).toBe('');
        wrapper.vm.onErrorForHandlerUpdateLanguage({name: 'ErrorName'});
        
        expect(wrapper.vm.errorsName).toBe('ErrorName');
    });
    
    it("Проверка функции onErrorForHandlerUpdateLanguage (ошибка сервера с message)", async () => {
        const wrapper = getWrapper();
        
        expect(hideModal).toHaveBeenCalledTimes(0);
        wrapper.vm.onErrorForHandlerUpdateLanguage({ message: 'В таблице thesaurus.languages нет записи с id=13' });
        expect(hideModal).toHaveBeenCalledTimes(1);
    });
    
    it("Проверка функции onFinishForHandlerUpdateLanguage", async () => {
        app.isRequest = true;
        const wrapper = getWrapper(true);
        
        wrapper.vm.onFinishForHandlerUpdateLanguage();
        expect(app.isRequest).toBe(false);
    });
});
