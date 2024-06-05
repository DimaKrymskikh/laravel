import { mount } from "@vue/test-utils";

import { router } from '@inertiajs/vue3';

import { setActivePinia, createPinia } from 'pinia';
import AdminModal from '@/Components/Modal/Request/AdminModal.vue';
import { useAppStore } from '@/Stores/app';
import { useFilmsAccountStore } from '@/Stores/films';

import { checkBaseModal } from '@/__tests__/methods/checkBaseModal';
import { checkInputField } from '@/__tests__/methods/checkInputField';
import { eventCurrentTargetClassListContainsFalse } from '@/__tests__/fake/Event';

vi.mock('@inertiajs/vue3');
        
const hideAdminModal = vi.fn();

const getWrapper = function(app, filmsAccount, admin = false) {
    return mount(AdminModal, {
            props: {
                hideAdminModal,
                admin
            },
            global: {
                provide: { app, filmsAccount }
            }
        });
};

const checkContentForNotAdmin = function(wrapper) {
    // Проверка равенства переменных ref начальным данным
    expect(wrapper.vm.inputPassword).toBe('');
    expect(wrapper.vm.errorsPassword).toBe('');

    // Заголовок модального окна задаётся
    expect(wrapper.text()).toContain('Подтверждение статуса админа');
    // Содержится вопрос
    expect(wrapper.text()).toContain('Вы хотите получить права админа?');
};

const checkContentForAdmin = function(wrapper) {
    // Проверка равенства переменных ref начальным данным
    expect(wrapper.vm.inputPassword).toBe('');
    expect(wrapper.vm.errorsPassword).toBe('');

    // Заголовок модального окна задаётся
    expect(wrapper.text()).toContain('Отказ от статуса админа');
    // Содержится вопрос
    expect(wrapper.text()).toContain('Вы хотите отказаться от статуса админа?');
};

describe("@/Components/Modal/Request/AdminModal.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    
    it("Монтирование компоненты AdminModal (isRequest: false, admin: false)", async () => {
        const app = useAppStore();
        const filmsAccount = useFilmsAccountStore();

        const wrapper = getWrapper(app, filmsAccount);
        
        checkContentForNotAdmin(wrapper);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 1);
        checkInputField.checkPropsInputField(inputFields[0], 'Введите пароль:', 'password', wrapper.vm.errorsPassword, wrapper.vm.inputPassword, true);
        checkInputField.checkInputFieldWhenThereIsNoRequest(inputFields[0], wrapper.vm.inputPassword, 'TestPassword');
        
        await checkBaseModal.hideBaseModal(wrapper, hideAdminModal);
        await checkBaseModal.submitRequestInBaseModal(wrapper, router.post);
    });
    
    it("Монтирование компоненты AdminModal (isRequest: true, admin: false)", async () => {
        const app = useAppStore();
        app.isRequest = true;
        
        const filmsAccount = useFilmsAccountStore();

        const wrapper = getWrapper(app, filmsAccount);
        
        checkContentForNotAdmin(wrapper);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 1);
        checkInputField.checkPropsInputField(inputFields[0], 'Введите пароль:', 'password', wrapper.vm.errorsPassword, wrapper.vm.inputPassword, true);
        checkInputField.checkInputFieldWhenRequestIsMade(inputFields[0], wrapper.vm.inputPassword, 'TestPassword');
        
        await checkBaseModal.notHideBaseModal(wrapper, hideAdminModal);
        await checkBaseModal.notSubmitRequestInBaseModal(wrapper, router.post);
    });
    
    it("Монтирование компоненты AdminModal (isRequest: false, admin: true)", async () => {
        const app = useAppStore();
        const filmsAccount = useFilmsAccountStore();

        const wrapper = getWrapper(app, filmsAccount, true);
        
        checkContentForAdmin(wrapper);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 1);
        checkInputField.checkPropsInputField(inputFields[0], 'Введите пароль:', 'password', wrapper.vm.errorsPassword, wrapper.vm.inputPassword, true);
        checkInputField.checkInputFieldWhenThereIsNoRequest(inputFields[0], wrapper.vm.inputPassword, 'TestPassword');
        
        await checkBaseModal.hideBaseModal(wrapper, hideAdminModal);
        await checkBaseModal.submitRequestInBaseModal(wrapper, router.post);
    });
    
    it("Монтирование компоненты AdminModal (isRequest: true, admin: true)", async () => {
        const app = useAppStore();
        app.isRequest = true;
        
        const filmsAccount = useFilmsAccountStore();

        const wrapper = getWrapper(app, filmsAccount, true);
        
        checkContentForAdmin(wrapper);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 1);
        checkInputField.checkPropsInputField(inputFields[0], 'Введите пароль:', 'password', wrapper.vm.errorsPassword, wrapper.vm.inputPassword, true);
        checkInputField.checkInputFieldWhenRequestIsMade(inputFields[0], wrapper.vm.inputPassword, 'TestPassword');
        
        await checkBaseModal.notHideBaseModal(wrapper, hideAdminModal);
        await checkBaseModal.notSubmitRequestInBaseModal(wrapper, router.post);
    });
    
    it("Функция handlerSubmit вызывает router.post с нужными параметрами (admin: false)", () => {
        const app = useAppStore();
        const filmsAccount = useFilmsAccountStore();

        const wrapper = getWrapper(app, filmsAccount, false);
        const options = {
            onBefore: expect.anything(),
            onSuccess: expect.anything(),
            onError: expect.anything(),
            onFinish: expect.anything()
        };
        
        wrapper.vm.handlerSubmit(eventCurrentTargetClassListContainsFalse);
        
        expect(router.post).toHaveBeenCalledTimes(1);
        expect(router.post).toHaveBeenCalledWith(filmsAccount.getUrl('admin/create'), {
                password: wrapper.vm.inputPassword
            }, options);
    });
    
    it("Функция handlerSubmit вызывает router.post с нужными параметрами (admin: true)", () => {
        const app = useAppStore();
        const filmsAccount = useFilmsAccountStore();

        const wrapper = getWrapper(app, filmsAccount, true);
        const options = {
            onBefore: expect.anything(),
            onSuccess: expect.anything(),
            onError: expect.anything(),
            onFinish: expect.anything()
        };
        
        wrapper.vm.handlerSubmit(eventCurrentTargetClassListContainsFalse);
        
        expect(router.post).toHaveBeenCalledTimes(1);
        expect(router.post).toHaveBeenCalledWith(filmsAccount.getUrl('admin/destroy'), {
                password: wrapper.vm.inputPassword
            }, options);
    });
    
    it("Проверка функции onBeforeForHandlerSubmit", () => {
        const app = useAppStore();
        // По умолчанию
        expect(app.isRequest).toBe(false);
        const filmsAccount = useFilmsAccountStore();

        const wrapper = getWrapper(app, filmsAccount, false);
        wrapper.vm.errorsPassword = 'ErrorPassword';
        wrapper.vm.onBeforeForHandlerSubmit();
        
        expect(app.isRequest).toBe(true);
        expect(wrapper.vm.errorsPassword).toBe('');
    });
    
    it("Проверка функции onSuccessForHandlerSubmit", async () => {
        const app = useAppStore();
        const filmsAccount = useFilmsAccountStore();

        const wrapper = getWrapper(app, filmsAccount, true);
        
        expect(hideAdminModal).not.toHaveBeenCalled();
        wrapper.vm.onSuccessForHandlerSubmit();
        
        expect(hideAdminModal).toHaveBeenCalledTimes(1);
        expect(hideAdminModal).toHaveBeenCalledWith();
    });
    
    it("Проверка функции onErrorForHandlerSubmit", async () => {
        const app = useAppStore();
        const filmsAccount = useFilmsAccountStore();
        
        const wrapper = getWrapper(app, filmsAccount, false);
        
        expect(wrapper.vm.errorsPassword).toBe('');
        wrapper.vm.onErrorForHandlerSubmit({ password: 'ErrorPassword' });
        
        expect(wrapper.vm.errorsPassword).toBe('ErrorPassword');
    });
    
    it("Проверка функции onFinishForHandlerSubmit", async () => {
        const app = useAppStore();
        app.isRequest = true;
        const filmsAccount = useFilmsAccountStore();
        
        const wrapper = getWrapper(app, filmsAccount);
        wrapper.vm.onFinishForHandlerSubmit();
        
        expect(app.isRequest).toBe(false);
    });
});
