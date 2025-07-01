import { mount } from "@vue/test-utils";
import { router } from '@inertiajs/vue3';
import { setActivePinia, createPinia } from 'pinia';

import { actor } from '@/Services/Content/actors';
import { app } from '@/Services/app';
import RemoveActorModal from '@/Components/Modal/Request/Actors/RemoveActorModal.vue';
import { useActorsListStore } from '@/Stores/actors';

import { checkBaseModal } from '@/__tests__/methods/checkBaseModal';
import { checkInputField } from '@/__tests__/methods/checkInputField';
import { eventCurrentTargetClassListContainsFalse } from '@/__tests__/fake/Event';

vi.mock('@inertiajs/vue3');
        
const hideModal = vi.spyOn(actor, 'hideRemoveActorModal');

const getWrapper = function() {
    return mount(RemoveActorModal, {
            global: {
                provide: {
                    actorsList: useActorsListStore()
                }
            }
        });
};

const checkContent = function(wrapper) {
    // Проверка равенства переменных ref начальным данным
    expect(wrapper.vm.inputPassword).toBe('');
    expect(wrapper.vm.errorsPassword).toBe('');

    // Заголовок модального окна задаётся
    expect(wrapper.text()).toContain('Подтверждение удаления актёра');
    // Содержится вопрос модального окна с нужными параметрами
    expect(wrapper.text()).toContain('Вы действительно хотите удалить актёра');
};

describe("@/Components/Modal/Request/Actors/RemoveActorModal.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    
    it("Монтирование компоненты RemoveActorModal (isRequest: false)", async () => {
        const wrapper = getWrapper();
        
        checkContent(wrapper);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 1);
        checkInputField.checkPropsInputField(inputFields[0], 'Введите пароль:', 'password', wrapper.vm.errorsPassword, wrapper.vm.inputPassword, true);
        checkInputField.checkInputFieldWhenThereIsNoRequest(inputFields[0], wrapper.vm.inputPassword, 'TestPassword');
        
        await checkBaseModal.hideBaseModal(wrapper, hideModal);
        await checkBaseModal.submitRequestInBaseModal(wrapper, router.delete);
    });
    
    it("Монтирование компоненты RemoveActorModal (isRequest: true)", async () => {
        app.isRequest = true;
        const wrapper = getWrapper();
        
        checkContent(wrapper);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 1);
        checkInputField.checkPropsInputField(inputFields[0], 'Введите пароль:', 'password', wrapper.vm.errorsPassword, wrapper.vm.inputPassword, true);
        checkInputField.checkInputFieldWhenRequestIsMade(inputFields[0], wrapper.vm.inputPassword, 'TestPassword');
        
        await checkBaseModal.notHideBaseModal(wrapper, hideModal);
        await checkBaseModal.notSubmitRequestInBaseModal(wrapper, router.delete);
    });
    
    it("Функция handlerRemoveActor вызывает router.delete с нужными параметрами", () => {
        const wrapper = getWrapper();
        const options = {
            data: {
                password: wrapper.vm.inputPassword
            },
            preserveScroll: true,
            onBefore: expect.anything(),
            onSuccess: expect.anything(),
            onError: expect.anything(),
            onFinish: expect.anything()
        };
        
        wrapper.vm.handlerRemoveActor(eventCurrentTargetClassListContainsFalse);
        
        expect(router.delete).toHaveBeenCalledTimes(1);
        expect(router.delete).toHaveBeenCalledWith(wrapper.vm.actorsList.getUrl(actor.id), options);
    });
    
    it("Проверка функции onBeforeForHandlerRemoveActor", () => {
        const wrapper = getWrapper();
        
        wrapper.vm.errorsPassword = 'ErrorPassword';
        wrapper.vm.onBeforeForHandlerRemoveActor();
        
        expect(app.isRequest).toBe(true);
        expect(wrapper.vm.errorsPassword).toBe('');
    });
    
    it("Проверка функции onSuccessForHandlerRemoveActor", async () => {
        const wrapper = getWrapper();
        
        expect(hideModal).not.toHaveBeenCalled();
        wrapper.vm.onSuccessForHandlerRemoveActor();
        
        expect(hideModal).toHaveBeenCalledTimes(1);
        expect(hideModal).toHaveBeenCalledWith();
    });
    
    it("Проверка функции onErrorForHandlerRemoveActor ({ password: 'ErrorPassword' })", async () => {
        const wrapper = getWrapper();
        
        expect(wrapper.vm.errorsPassword).toBe('');
        expect(app.isShowForbiddenModal).toBe(false);
        
        wrapper.vm.onErrorForHandlerRemoveActor({ password: 'ErrorPassword' });
        
        expect(wrapper.vm.errorsPassword).toBe('ErrorPassword');
        expect(app.errorMessage).toBe('');
        expect(app.isShowForbiddenModal).toBe(false);
    });
    
    it("Проверка функции onErrorForHandlerRemoveActor ({ message: 'ServerError' })", async () => {
        const wrapper = getWrapper();
        
        expect(wrapper.vm.errorsPassword).toBe('');
        expect(app.isShowForbiddenModal).toBe(false);
        
        wrapper.vm.onErrorForHandlerRemoveActor({ message: 'ServerError' });
        
        expect(wrapper.vm.errorsPassword).toBe('');
        expect(app.errorMessage).toBe('ServerError');
        expect(app.isShowForbiddenModal).toBe(true);
    });
    
    it("Проверка функции onFinishForHandlerRemoveActor", async () => {
        app.isRequest = true;
        const wrapper = getWrapper(true);
        
        wrapper.vm.onFinishForHandlerRemoveActor();
        expect(app.isRequest).toBe(false);
    });
});
