import { mount } from "@vue/test-utils";

import { router } from '@inertiajs/vue3';

import { setActivePinia, createPinia } from 'pinia';
import UpdateCityModal from '@/Components/Modal/Request/Cities/UpdateCityModal.vue';
import { useAppStore } from '@/Stores/app';

import { checkBaseModal } from '@/__tests__/methods/checkBaseModal';
import { checkInputField } from '@/__tests__/methods/checkInputField';

vi.mock('@inertiajs/vue3');
        
const hideUpdateCityModal = vi.fn();

const getWrapper = function(app) {
    return mount(UpdateCityModal, {
            props: {
                hideUpdateCityModal,
                updateCity: {
                    id: '5',
                    name: 'Город'
                }
            },
            global: {
                provide: { app }
            }
        });
};

const checkContent = function(wrapper) {
    // Проверка равенства переменных ref начальным данным
    expect(wrapper.vm.cityName).toBe('Город');
    expect(wrapper.vm.errorsName).toBe('');

    // Заголовок модального окна задаётся
    expect(wrapper.text()).toContain('Изменение названия города');
};

describe("@/Components/Modal/Request/UpdateCityModal.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    
    it("Монтирование компоненты UpdateCityModal (isRequest: false)", async () => {
        const app = useAppStore();

        const wrapper = getWrapper(app);
        
        checkContent(wrapper);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 1);
        checkInputField.checkPropsInputField(inputFields[0], 'Имя города:', 'text', wrapper.vm.errorsName, wrapper.vm.cityName, true);
        checkInputField.checkInputFieldWhenThereIsNoRequest(inputFields[0], wrapper.vm.cityName, 'Имя нового города');
        
        await checkBaseModal.hideBaseModal(wrapper, hideUpdateCityModal);
        await checkBaseModal.submitRequestInBaseModal(wrapper, router.put);
    });
    
    it("Монтирование компоненты UpdateCityModal (isRequest: true)", async () => {
        const app = useAppStore();
        app.isRequest = true;

        const wrapper = getWrapper(app);
        
        checkContent(wrapper);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 1);
        checkInputField.checkPropsInputField(inputFields[0], 'Имя города:', 'text', wrapper.vm.errorsName, wrapper.vm.cityName, true);
        checkInputField.checkInputFieldWhenRequestIsMade(inputFields[0], wrapper.vm.cityName, 'Имя нового города');
        
        await checkBaseModal.notHideBaseModal(wrapper, hideUpdateCityModal);
        await checkBaseModal.notSubmitRequestInBaseModal(wrapper, router.put);
    });
});
