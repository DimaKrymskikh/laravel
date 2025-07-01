import { mount } from "@vue/test-utils";

import { router } from '@inertiajs/vue3';

import AccountRemoveModal from '@/Components/Modal/Request/AccountRemoveModal.vue';
import { app } from '@/Services/app';

import { checkBaseModal } from '@/__tests__/methods/checkBaseModal';
import { checkInputField } from '@/__tests__/methods/checkInputField';
import { eventCurrentTargetClassListContainsFalse } from '@/__tests__/fake/Event';

vi.mock('@inertiajs/vue3');
        
const hideAccountRemoveModal = vi.fn();

const getWrapper = function() {
    return mount(AccountRemoveModal, {
            props: {
                hideAccountRemoveModal
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
    it("Монтирование компоненты AccountRemoveModal (isRequest: false)", async () => {
        const wrapper = getWrapper();
        
        checkContent(wrapper);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 1);
        checkInputField.checkPropsInputField(inputFields[0], 'Введите пароль:', 'password', wrapper.vm.errorsPassword, wrapper.vm.inputPassword, true);
        checkInputField.checkInputFieldWhenThereIsNoRequest(inputFields[0], wrapper.vm.inputPassword, 'TestPassword');
        
        await checkBaseModal.hideBaseModal(wrapper, hideAccountRemoveModal);
        
        await checkBaseModal.submitRequestInBaseModal(wrapper, router.delete);
    });
    
    it("Монтирование компоненты AccountRemoveModal (isRequest: true)", async () => {
        app.isRequest = true;
        const wrapper = getWrapper();
        
        checkContent(wrapper);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 1);
        checkInputField.checkPropsInputField(inputFields[0], 'Введите пароль:', 'password', wrapper.vm.errorsPassword, wrapper.vm.inputPassword, true);
        checkInputField.checkInputFieldWhenRequestIsMade(inputFields[0], wrapper.vm.inputPassword, 'TestPassword');
        
        await checkBaseModal.notHideBaseModal(wrapper, hideAccountRemoveModal);
        
        await checkBaseModal.notSubmitRequestInBaseModal(wrapper, router.delete);
    });
    
    it("Функция handlerRemoveAccount вызывает router.delete с нужными параметрами", () => {
        const wrapper = getWrapper();
        
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
        const wrapper = getWrapper();
        
        wrapper.vm.errorsPassword = 'ErrorPassword';
        wrapper.vm.onBeforeForHandlerRemoveAccount();
        
        expect(app.isRequest).toBe(true);
        expect(wrapper.vm.errorsPassword).toBe('');
    });
    
    it("Проверка функции onSuccessForHandlerRemoveAccount", async () => {
        const wrapper = getWrapper();
        
        expect(hideAccountRemoveModal).not.toHaveBeenCalled();
        wrapper.vm.onSuccessForHandlerRemoveAccount();
        
        expect(hideAccountRemoveModal).toHaveBeenCalledTimes(1);
        expect(hideAccountRemoveModal).toHaveBeenCalledWith();
    });
    
    it("Проверка функции onErrorForHandlerRemoveAccount", async () => {
        const wrapper = getWrapper();
        
        expect(wrapper.vm.errorsPassword).toBe('');
        wrapper.vm.onErrorForHandlerRemoveAccount({ password: 'ErrorPassword' });
        
        expect(wrapper.vm.errorsPassword).toBe('ErrorPassword');
    });
    
    it("Проверка функции onFinishForHandlerRemoveAccount", async () => {
        app.isRequest = true;
        const wrapper = getWrapper(app);
        
        wrapper.vm.inputPassword = 'TestPassword';
        wrapper.vm.onFinishForHandlerRemoveAccount();
        
        expect(app.isRequest).toBe(false);
        expect(wrapper.vm.inputPassword).toBe('');
    });
});
