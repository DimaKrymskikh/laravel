import { mount } from "@vue/test-utils";

import { router } from '@inertiajs/vue3';

import { setActivePinia, createPinia } from 'pinia';
import AddCityModal from '@/Components/Modal/Request/Cities/AddCityModal.vue';
import { useAppStore } from '@/Stores/app';

import { checkBaseModal } from '@/__tests__/methods/checkBaseModal';
import { checkInputField } from '@/__tests__/methods/checkInputField';

vi.mock('@inertiajs/vue3');
        
const hideAddCityModal = vi.fn();

const getWrapper = function(app) {
    return mount(AddCityModal, {
            props: {
                hideAddCityModal
            },
            global: {
                provide: { app }
            }
        });
};

const checkContent = function(wrapper) {
    // Проверка равенства переменных ref начальным данным
    expect(wrapper.vm.cityName).toBe('');
    expect(wrapper.vm.openWeatherId).toBe('');
    expect(wrapper.vm.errorsName).toBe('');
    expect(wrapper.vm.errorsOpenWeatherId).toBe('');

    // Заголовок модального окна задаётся
    expect(wrapper.text()).toContain('Добавление города');
};
        
describe("@/Components/Modal/Request/AddCityModal.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    
    it("Монтирование компоненты AddCityModal (isRequest: false)", async () => {
        const app = useAppStore();

        const wrapper = getWrapper(app);
        
        checkContent (wrapper);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 2);
        checkInputField.checkPropsInputField(inputFields[0], 'Имя города:', 'text', wrapper.vm.errorsName, wrapper.vm.cityName, true);
        checkInputField.checkPropsInputField(inputFields[1], 'Id города в OpenWeather:', 'text', wrapper.vm.errorsOpenWeatherId, wrapper.vm.openWeatherId);
        
        checkInputField.checkInputFieldWhenThereIsNoRequest(inputFields[0], wrapper.vm.cityName, 'Имя города');
        checkInputField.checkInputFieldWhenThereIsNoRequest(inputFields[1], wrapper.vm.openWeatherId, '7777');
        
        await checkBaseModal.hideBaseModal(wrapper, hideAddCityModal);
        await checkBaseModal.submitRequestInBaseModal(wrapper, router.post);
    });
    
    it("Монтирование компоненты AddCityModal (isRequest: true)", async () => {
        const app = useAppStore();
        app.isRequest = true;

        const wrapper = getWrapper(app);
        
        checkContent (wrapper);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 2);
        checkInputField.checkPropsInputField(inputFields[0], 'Имя города:', 'text', wrapper.vm.errorsName, wrapper.vm.cityName, true);
        checkInputField.checkPropsInputField(inputFields[1], 'Id города в OpenWeather:', 'text', wrapper.vm.errorsOpenWeatherId, wrapper.vm.openWeatherId);
        
        checkInputField.checkInputFieldWhenRequestIsMade(inputFields[0], wrapper.vm.cityName, 'Имя города');
        checkInputField.checkInputFieldWhenRequestIsMade(inputFields[1], wrapper.vm.openWeatherId, '7777');
        
        await checkBaseModal.notHideBaseModal(wrapper, hideAddCityModal);
        await checkBaseModal.notSubmitRequestInBaseModal(wrapper, router.post);
    });
});
