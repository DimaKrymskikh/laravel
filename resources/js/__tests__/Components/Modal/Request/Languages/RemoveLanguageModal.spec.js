import { mount } from "@vue/test-utils";
import { router } from '@inertiajs/vue3';

import { setActivePinia, createPinia } from 'pinia';
import RemoveLanguageModal from '@/Components/Modal/Request/Languages/RemoveLanguageModal.vue';
import { useAppStore } from '@/Stores/app';

import { checkBaseModal } from '@/__tests__/methods/checkBaseModal';
import { checkInputField } from '@/__tests__/methods/checkInputField';

vi.mock('@inertiajs/vue3');
        
const hideRemoveLanguageModal = vi.fn();

const getWrapper = function(app) {
    return mount(RemoveLanguageModal, {
            props: {
                hideRemoveLanguageModal,
                removeLanguage: {
                    id: 1,
                    name: 'Русский'
                }
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
    expect(wrapper.text()).toContain('Подтверждение удаления языка');
    // Содержится вопрос модального окна
    expect(wrapper.text()).toContain('Вы действительно хотите удалить Русский язык?');
};

describe("@/Components/Modal/Request/Languages/RemoveLanguageModal.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    
    it("Монтирование компоненты RemoveLanguageModal (isRequest: false)", async () => {
        const app = useAppStore();

        const wrapper = getWrapper(app);
        
        checkContent(wrapper);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 1);
        checkInputField.checkPropsInputField(inputFields[0], 'Введите пароль:', 'password', wrapper.vm.errorsPassword, wrapper.vm.inputPassword, true);
        checkInputField.checkInputFieldWhenThereIsNoRequest(inputFields[0], wrapper.vm.inputPassword, 'TestPassword');
        
        await checkBaseModal.hideBaseModal(wrapper, hideRemoveLanguageModal);
        await checkBaseModal.submitRequestInBaseModal(wrapper, router.delete);
    });
    
    it("Монтирование компоненты RemoveLanguageModal (isRequest: true)", async () => {
        const app = useAppStore();
        app.isRequest = true;

        const wrapper = getWrapper(app);
        
        checkContent(wrapper);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 1);
        checkInputField.checkPropsInputField(inputFields[0], 'Введите пароль:', 'password', wrapper.vm.errorsPassword, wrapper.vm.inputPassword, true);
        checkInputField.checkInputFieldWhenRequestIsMade(inputFields[0], wrapper.vm.inputPassword, 'TestPassword');
        
        await checkBaseModal.notHideBaseModal(wrapper, hideRemoveLanguageModal);
        await checkBaseModal.notSubmitRequestInBaseModal(wrapper, router.delete);
    });
});
