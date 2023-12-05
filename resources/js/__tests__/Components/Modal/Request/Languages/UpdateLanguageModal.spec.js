import { mount } from "@vue/test-utils";
import { router } from '@inertiajs/vue3';

import { setActivePinia, createPinia } from 'pinia';
import UpdateLanguageModal from '@/Components/Modal/Request/Languages/UpdateLanguageModal.vue';
import { useAppStore } from '@/Stores/app';

import { checkBaseModal } from '@/__tests__/methods/checkBaseModal';
import { checkInputField } from '@/__tests__/methods/checkInputField';

vi.mock('@inertiajs/vue3');
        
const hideUpdateLanguageModal = vi.fn();

const getWrapper = function(app) {
    return mount(UpdateLanguageModal, {
            props: {
                hideUpdateLanguageModal,
                updateLanguage: {
                    id: '1',
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
    expect(wrapper.vm.languageName).toBe('Русский');
    expect(wrapper.vm.errorsName).toBe('');

    // Заголовок модального окна задаётся
    expect(wrapper.text()).toContain('Изменение названия языка');
};

describe("@/Components/Modal/Request/Languages/UpdateLanguageModal.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    
    it("Монтирование компоненты UpdateLanguageModal (isRequest: false)", async () => {
        const app = useAppStore();

        const wrapper = getWrapper(app);
        
        checkContent(wrapper);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 1);
        checkInputField.checkPropsInputField(inputFields[0], 'Имя языка:', 'text', wrapper.vm.errorsName, wrapper.vm.languageName, true);
        checkInputField.checkInputFieldWhenThereIsNoRequest(inputFields[0], wrapper.vm.languageName, 'Английский');
        
        await checkBaseModal.hideBaseModal(wrapper, hideUpdateLanguageModal);
        await checkBaseModal.submitRequestInBaseModal(wrapper, router.put);
    });
    
    it("Монтирование компоненты UpdateLanguageModal (isRequest: true)", async () => {
        const app = useAppStore();
        app.isRequest = true;

        const wrapper = getWrapper(app);
        
        checkContent(wrapper);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 1);
        checkInputField.checkPropsInputField(inputFields[0], 'Имя языка:', 'text', wrapper.vm.errorsName, wrapper.vm.languageName, true);
        checkInputField.checkInputFieldWhenRequestIsMade(inputFields[0], wrapper.vm.languageName, 'Английский');
        
        await checkBaseModal.notHideBaseModal(wrapper, hideUpdateLanguageModal);
        await checkBaseModal.notSubmitRequestInBaseModal(wrapper, router.put);
    });
});
