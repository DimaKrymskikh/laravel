import { mount } from "@vue/test-utils";
import { router } from '@inertiajs/vue3';

import RemoveCityFromListOfWeatherModal from '@/Components/Pages/Auth/Account/UserWeather/RemoveCityFromListOfWeatherModal.vue';
import { app } from '@/Services/app';

import { checkBaseModal } from '@/__tests__/methods/checkBaseModal';
import { checkInputField } from '@/__tests__/methods/checkInputField';
import { eventCurrentTargetClassListContainsFalse } from '@/__tests__/fake/Event';

vi.mock('@inertiajs/vue3', async () => {
    const actual = await vi.importActual("@inertiajs/vue3");
    return {
        ...actual,
        router: {
            delete: vi.fn()
        }
    };
});
        
const hideRemoveCityModal = vi.fn();

const getWrapper = function() {
    return mount(RemoveCityFromListOfWeatherModal, {
            props: {
                hideRemoveCityModal,
                removeCity: {
                    id: 7,
                    name: 'Некоторый город'
                }
            }
        });
};

const checkContent = function(wrapper) {
    // Проверка равенства переменных ref начальным данным
    expect(wrapper.vm.inputPassword).toBe('');
    expect(wrapper.vm.errorsPassword).toBe('');

    // Заголовок модального окна задаётся
    expect(wrapper.text()).toContain('Подтверждение удаления города');
    // Содержится вопрос модального окна с нужными параметрами
    expect(wrapper.text()).toContain('Вы действительно хотите удалить город');
    expect(wrapper.text()).toContain(wrapper.vm.removeCity.name);
};

describe("@/Components/Pages/Auth/Account/UserWeather/RemoveCityFromListOfWeatherModal.vue", () => {
    it("Монтирование компоненты RemoveCityModal (isRequest: false)", async () => {
        const wrapper = getWrapper();
        
        checkContent(wrapper);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 1);
        checkInputField.checkPropsInputField(inputFields[0], 'Введите пароль:', 'password', wrapper.vm.errorsPassword, wrapper.vm.inputPassword, true);
        checkInputField.checkInputFieldWhenThereIsNoRequest(inputFields[0], wrapper.vm.inputPassword, 'TestPassword');
        
        await checkBaseModal.hideBaseModal(wrapper, hideRemoveCityModal);
        await checkBaseModal.submitRequestInBaseModal(wrapper, router.delete);
    });
    
    it("Монтирование компоненты RemoveCityModal (isRequest: true)", async () => {
        app.isRequest = true;
        const wrapper = getWrapper();
        
        checkContent(wrapper);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 1);
        checkInputField.checkPropsInputField(inputFields[0], 'Введите пароль:', 'password', wrapper.vm.errorsPassword, wrapper.vm.inputPassword, true);
        checkInputField.checkInputFieldWhenRequestIsMade(inputFields[0], wrapper.vm.inputPassword, 'TestPassword');
        
        await checkBaseModal.notHideBaseModal(wrapper, hideRemoveCityModal);
        await checkBaseModal.notSubmitRequestInBaseModal(wrapper, router.delete);
    });
    
    it("Функция handlerRemoveCity вызывает router.delete с нужными параметрами", () => {
        const wrapper = getWrapper();
        
        const options = {
            preserveScroll: true,
            data: {
                password: wrapper.vm.inputPassword
            },
            onBefore: expect.anything(),
            onSuccess: expect.anything(),
            onError: expect.anything(),
            onFinish: expect.anything()
        };
        
        wrapper.vm.handlerRemoveCity(eventCurrentTargetClassListContainsFalse);
        
        expect(router.delete).toHaveBeenCalledTimes(1);
        expect(router.delete).toHaveBeenCalledWith(`cities/removecity/${wrapper.vm.removeCity.id}`, options);
    });
    
    it("Проверка функции onBeforeForHandlerRemoveCity", () => {
        const wrapper = getWrapper();
        
        wrapper.vm.errorsPassword = 'TestPassword';
        wrapper.vm.onBeforeForHandlerRemoveCity();
        
        expect(app.isRequest).toBe(true);
        expect(wrapper.vm.errorsPassword).toBe('');
    });
    
    it("Проверка функции onSuccessForHandlerRemoveCity", async () => {
        const wrapper = getWrapper();
        
        expect(hideRemoveCityModal).not.toHaveBeenCalled();
        wrapper.vm.onSuccessForHandlerRemoveCity();
        
        expect(hideRemoveCityModal).toHaveBeenCalledTimes(1);
        expect(hideRemoveCityModal).toHaveBeenCalledWith();
    });
    
    it("Проверка функции onErrorForHandlerRemoveCity", async () => {
        const wrapper = getWrapper();
        
        expect(wrapper.vm.errorsPassword).toBe('');
        wrapper.vm.onErrorForHandlerRemoveCity({password: 'ErrorPassword'});
        
        expect(wrapper.vm.errorsPassword).toBe('ErrorPassword');
    });
    
    it("Проверка функции onFinishForHandlerRemoveCity", async () => {
        app.isRequest = true;
        const wrapper = getWrapper();
        
        wrapper.vm.onFinishForHandlerRemoveCity();
        expect(app.isRequest).toBe(false);
    });
});
