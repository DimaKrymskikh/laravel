import { mount } from "@vue/test-utils";

import { router } from '@inertiajs/vue3';

import { setActivePinia, createPinia } from 'pinia';
import AddLanguageModal from '@/Components/Modal/Request/Languages/AddLanguageModal.vue';
import { useAppStore } from '@/Stores/app';

import { checkBaseModal } from '@/__tests__/methods/checkBaseModal';
import { checkInputField } from '@/__tests__/methods/checkInputField';
import { eventCurrentTargetClassListContainsFalse } from '@/__tests__/fake/Event';

vi.mock('@inertiajs/vue3');
        
const hideAddLanguageModal = vi.fn();

const getWrapper = function(app) {
    return mount(AddLanguageModal, {
            props: {
                hideAddLanguageModal
            },
            global: {
                provide: { app }
            }
        });
};

const checkContent = function(wrapper) {
    // Проверка равенства переменных ref начальным данным
    expect(wrapper.vm.languageName).toBe('');
    expect(wrapper.vm.errorsName).toBe('');

    // Заголовок модального окна задаётся
    expect(wrapper.text()).toContain('Добавление языка');
};
        
describe("@/Components/Modal/Request/Languages/AddLanguageModal.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    
    it("Монтирование компоненты AddLanguageModal (isRequest: false)", async () => {
        const app = useAppStore();

        const wrapper = getWrapper(app);
        
        checkContent (wrapper);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 1);
        checkInputField.checkPropsInputField(inputFields[0], 'Имя языка:', 'text', wrapper.vm.errorsName, wrapper.vm.languageName, true);
        
        checkInputField.checkInputFieldWhenThereIsNoRequest(inputFields[0], wrapper.vm.languageName, 'Китайский');
        
        await checkBaseModal.hideBaseModal(wrapper, hideAddLanguageModal);
        await checkBaseModal.submitRequestInBaseModal(wrapper, router.post);
    });
    
    it("Монтирование компоненты AddLanguageModal (isRequest: true)", async () => {
        const app = useAppStore();
        app.isRequest = true;

        const wrapper = getWrapper(app);
        
        checkContent (wrapper);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 1);
        checkInputField.checkPropsInputField(inputFields[0], 'Имя языка:', 'text', wrapper.vm.errorsName, wrapper.vm.languageName, true);
        
        checkInputField.checkInputFieldWhenRequestIsMade(inputFields[0], wrapper.vm.languageName, 'Китайский');
        
        await checkBaseModal.notHideBaseModal(wrapper, hideAddLanguageModal);
        await checkBaseModal.notSubmitRequestInBaseModal(wrapper, router.post);
    });
    
    it("Функция handlerAddLanguage вызывает router.post с нужными параметрами", () => {
        const app = useAppStore();
        const options = {
            onBefore: expect.anything(),
            onSuccess: expect.anything(),
            onError: expect.anything(),
            onFinish: expect.anything()
        };
        
        const wrapper = getWrapper(app);
        
        wrapper.vm.handlerAddLanguage(eventCurrentTargetClassListContainsFalse);
        
        expect(router.post).toHaveBeenCalledTimes(1);
        expect(router.post).toHaveBeenCalledWith('/admin/languages', { name: wrapper.vm.languageName }, options);
    });
    
    it("Проверка функции onBeforeForHandlerAddLanguage", () => {
        const app = useAppStore();
        // По умолчанию
        expect(app.isRequest).toBe(false);
        
        const wrapper = getWrapper(app);
        wrapper.vm.errorsName = 'TestName';
        wrapper.vm.onBeforeForHandlerAddLanguage();
        
        expect(app.isRequest).toBe(true);
        expect(wrapper.vm.errorsName).toBe('');
    });
    
    it("Проверка функции onSuccessForHandlerAddLanguage", async () => {
        const app = useAppStore();
        
        const wrapper = getWrapper(app);
        
        expect(hideAddLanguageModal).not.toHaveBeenCalled();
        wrapper.vm.onSuccessForHandlerAddLanguage();
        
        expect(hideAddLanguageModal).toHaveBeenCalledTimes(1);
        expect(hideAddLanguageModal).toHaveBeenCalledWith();
    });
    
    it("Проверка функции onErrorForHandlerAddLanguage", async () => {
        const app = useAppStore();
        
        const wrapper = getWrapper(app);
        
        expect(wrapper.vm.errorsName).toBe('');
        wrapper.vm.onErrorForHandlerAddLanguage({name: 'ErrorName'});
        
        expect(wrapper.vm.errorsName).toBe('ErrorName');
    });
    
    it("Проверка функции onFinishForHandlerAddLanguage", async () => {
        const app = useAppStore();
        app.isRequest = true;
        
        const wrapper = getWrapper(app);
        wrapper.vm.onFinishForHandlerAddLanguage();
        
        expect(app.isRequest).toBe(false);
    });
});
