import { mount } from "@vue/test-utils";

import { router } from '@inertiajs/vue3';

import { setActivePinia, createPinia } from 'pinia';
import RemoveCityModal from '@/Components/Modal/Request/Cities/RemoveCityModal.vue';
import { useAppStore } from '@/Stores/app';

import { checkBaseModal } from '@/__tests__/methods/checkBaseModal';
import { checkInputField } from '@/__tests__/methods/checkInputField';

vi.mock('@inertiajs/vue3');
        
const hideRemoveCityModal = vi.fn();

const getWrapper = function(app, cityName, cityOpenWeatherId) {
    return mount(RemoveCityModal, {
            props: {
                hideRemoveCityModal,
                removeCity: {
                    id: 7,
                    name: cityName,
                    open_weather_id: cityOpenWeatherId
                }
            },
            global: {
                provide: { app }
            }
        });
};

const checkContent = function(wrapper, cityName, cityOpenWeatherId) {
    // Проверка равенства переменных ref начальным данным
    expect(wrapper.vm.inputPassword).toBe('');
    expect(wrapper.vm.errorsPassword).toBe('');

    // Заголовок модального окна задаётся
    expect(wrapper.text()).toContain('Подтверждение удаления города');
    // Содержится вопрос модального окна с нужными параметрами
    expect(wrapper.text()).toContain('Вы действительно хотите удалить город');
    expect(wrapper.text()).toContain(cityName);
    expect(wrapper.text()).toContain(cityOpenWeatherId);
};

describe("@/Components/Modal/Request/RemoveCityModal.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    
    it("Монтирование компоненты RemoveCityModal (isRequest: false)", async () => {
        const app = useAppStore();
        
        const cityName = 'Несуществующий город';
        const cityOpenWeatherId = '13700';

        const wrapper = getWrapper(app, cityName, cityOpenWeatherId);
        
        checkContent(wrapper, cityName, cityOpenWeatherId);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 1);
        checkInputField.checkPropsInputField(inputFields[0], 'Введите пароль:', 'password', wrapper.vm.errorsPassword, wrapper.vm.inputPassword, true);
        checkInputField.checkInputFieldWhenThereIsNoRequest(inputFields[0], wrapper.vm.inputPassword, 'TestPassword');
        
        await checkBaseModal.hideBaseModal(wrapper, hideRemoveCityModal);
        await checkBaseModal.submitRequestInBaseModal(wrapper, router.delete);
    });
    
    it("Монтирование компоненты RemoveCityModal (isRequest: true)", async () => {
        const app = useAppStore();
        app.isRequest = true;
        
        const cityName = 'Несуществующий город';
        const cityOpenWeatherId = '13700';

        const wrapper = getWrapper(app, cityName, cityOpenWeatherId);
        
        checkContent(wrapper, cityName, cityOpenWeatherId);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 1);
        checkInputField.checkPropsInputField(inputFields[0], 'Введите пароль:', 'password', wrapper.vm.errorsPassword, wrapper.vm.inputPassword, true);
        checkInputField.checkInputFieldWhenRequestIsMade(inputFields[0], wrapper.vm.inputPassword, 'TestPassword');
        
        await checkBaseModal.notHideBaseModal(wrapper, hideRemoveCityModal);
        await checkBaseModal.notSubmitRequestInBaseModal(wrapper, router.delete);
    });
});
