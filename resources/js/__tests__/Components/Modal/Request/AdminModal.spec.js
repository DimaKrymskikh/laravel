import { mount } from "@vue/test-utils";

import { router } from '@inertiajs/vue3';

import { setActivePinia, createPinia } from 'pinia';
import AdminModal from '@/Components/Modal/Request/AdminModal.vue';
import { useAppStore } from '@/Stores/app';
import { useFilmsAccountStore } from '@/Stores/films';

import { checkBaseModal } from '@/__tests__/methods/checkBaseModal';
import { checkInputField } from '@/__tests__/methods/checkInputField';

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
});
