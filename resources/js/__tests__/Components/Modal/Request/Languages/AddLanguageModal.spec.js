import { mount } from "@vue/test-utils";

import { router } from '@inertiajs/vue3';

import { setActivePinia, createPinia } from 'pinia';
import AddLanguageModal from '@/Components/Modal/Request/Languages/AddLanguageModal.vue';
import { useAppStore } from '@/Stores/app';

import { checkBaseModal } from '@/__tests__/methods/checkBaseModal';
import { checkInputField } from '@/__tests__/methods/checkInputField';

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
});
