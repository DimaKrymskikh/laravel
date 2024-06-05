import { mount } from "@vue/test-utils";

import { router } from '@inertiajs/vue3';

import { setActivePinia, createPinia } from 'pinia';
import AccountRemoveModal from '@/Components/Modal/Request/AccountRemoveModal.vue';
import { useAppStore } from '@/Stores/app';

import { checkBaseModal } from '@/__tests__/methods/checkBaseModal';
import { checkInputField } from '@/__tests__/methods/checkInputField';
import { eventCurrentTargetClassListContainsFalse } from '@/__tests__/fake/Event';

vi.mock('@inertiajs/vue3');
        
const hideAccountRemoveModal = vi.fn();

const getWrapper = function(app) {
    return mount(AccountRemoveModal, {
            props: {
                hideAccountRemoveModal
            },
            global: {
                provide: { app }
            }
        });
};

const checkContent = function(wrapper) {
    // Проверка равенства переменных ref начальным данным
    expect(wrapper.vm.inputPassword).toBe('');
    expect(wrapper.vm.errorsPassword).toBe('');

    // Заголовок модального окна задаётся
    expect(wrapper.text()).toContain('Подтверждение удаления аккаунта');
    // Содержится вопрос
    expect(wrapper.text()).toContain('Вы действительно хотите удалить свой аккаунт?');
};
        
describe("@/Components/Modal/Request/AccountRemoveModal.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    
    it("Монтирование компоненты AccountRemoveModal (isRequest: false)", async () => {
        const app = useAppStore();

        const wrapper = getWrapper(app);
        
        checkContent(wrapper);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 1);
        checkInputField.checkPropsInputField(inputFields[0], 'Введите пароль:', 'password', wrapper.vm.errorsPassword, wrapper.vm.inputPassword, true);
        checkInputField.checkInputFieldWhenThereIsNoRequest(inputFields[0], wrapper.vm.inputPassword, 'TestPassword');
        
        await checkBaseModal.hideBaseModal(wrapper, hideAccountRemoveModal);
        
        await checkBaseModal.submitRequestInBaseModal(wrapper, router.delete);
    });
    
    it("Монтирование компоненты AccountRemoveModal (isRequest: true)", async () => {
        const app = useAppStore();
        app.isRequest = true;

        const wrapper = getWrapper(app);
        
        checkContent(wrapper);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 1);
        checkInputField.checkPropsInputField(inputFields[0], 'Введите пароль:', 'password', wrapper.vm.errorsPassword, wrapper.vm.inputPassword, true);
        checkInputField.checkInputFieldWhenRequestIsMade(inputFields[0], wrapper.vm.inputPassword, 'TestPassword');
        
        await checkBaseModal.notHideBaseModal(wrapper, hideAccountRemoveModal);
        
        await checkBaseModal.notSubmitRequestInBaseModal(wrapper, router.delete);
    });
    
    it("Функция handlerRemoveAccount вызывает router.delete с нужными параметрами", () => {
        const app = useAppStore();

        const wrapper = getWrapper(app);
        const options = {
            data: {
                password: wrapper.vm.inputPassword
            },
            onBefore: expect.anything(),
            onSuccess: expect.anything(),
            onError: expect.anything(),
            onFinish: expect.anything()
        };
        
        wrapper.vm.handlerRemoveAccount(eventCurrentTargetClassListContainsFalse);
        
        expect(router.delete).toHaveBeenCalledTimes(1);
        expect(router.delete).toHaveBeenCalledWith('register', options);
    });
    
    it("Проверка функции onBeforeForHandlerRemoveAccount", () => {
        const app = useAppStore();
        // По умолчанию
        expect(app.isRequest).toBe(false);

        const wrapper = getWrapper(app);
        wrapper.vm.errorsPassword = 'ErrorPassword';
        wrapper.vm.onBeforeForHandlerRemoveAccount();
        
        expect(app.isRequest).toBe(true);
        expect(wrapper.vm.errorsPassword).toBe('');
    });
    
    it("Проверка функции onSuccessForHandlerRemoveAccount", async () => {
        const app = useAppStore();

        const wrapper = getWrapper(app);
        
        expect(hideAccountRemoveModal).not.toHaveBeenCalled();
        wrapper.vm.onSuccessForHandlerRemoveAccount();
        
        expect(hideAccountRemoveModal).toHaveBeenCalledTimes(1);
        expect(hideAccountRemoveModal).toHaveBeenCalledWith();
    });
    
    it("Проверка функции onErrorForHandlerRemoveAccount", async () => {
        const app = useAppStore();
        
        const wrapper = getWrapper(app);
        
        expect(wrapper.vm.errorsPassword).toBe('');
        wrapper.vm.onErrorForHandlerRemoveAccount({ password: 'ErrorPassword' });
        
        expect(wrapper.vm.errorsPassword).toBe('ErrorPassword');
    });
    
    it("Проверка функции onFinishForHandlerRemoveAccount", async () => {
        const app = useAppStore();
        app.isRequest = true;
        
        const wrapper = getWrapper(app);
        
        wrapper.vm.inputPassword = 'TestPassword';
        wrapper.vm.onFinishForHandlerRemoveAccount();
        
        expect(app.isRequest).toBe(false);
        expect(wrapper.vm.inputPassword).toBe('');
    });
});
