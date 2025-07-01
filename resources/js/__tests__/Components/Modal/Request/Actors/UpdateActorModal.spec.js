import { mount } from "@vue/test-utils";
import { router } from '@inertiajs/vue3';

import { setActivePinia, createPinia } from 'pinia';
import { actor } from '@/Services/Content/actors';
import { app } from '@/Services/app';
import UpdateActorModal from '@/Components/Modal/Request/Actors/UpdateActorModal.vue';
import { useActorsListStore } from '@/Stores/actors';

import { checkBaseModal } from '@/__tests__/methods/checkBaseModal';
import { checkInputField } from '@/__tests__/methods/checkInputField';
import { eventCurrentTargetClassListContainsFalse } from '@/__tests__/fake/Event';
import { actors } from '@/__tests__/data/actors';

vi.mock('@inertiajs/vue3');
        
const hideModal = vi.spyOn(actor, 'hideUpdateActorModal');

const getWrapper = function() {
    return mount(UpdateActorModal, {
            global: {
                provide: {
                    actorsList: useActorsListStore()
                }
            }
        });
};

const checkContent = function(wrapper) {
    // Проверка равенства переменных ref начальным данным
    expect(wrapper.vm.actorFirstName).toBe(actor.firstName);
    expect(wrapper.vm.actorLastName).toBe(actor.lastName);
    expect(wrapper.vm.errorsFirstName).toBe('');
    expect(wrapper.vm.errorsLastName).toBe('');

    // Заголовок модального окна задаётся
    expect(wrapper.text()).toContain('Изменение полного имени актёра');
};

describe("@/Components/Modal/Request/Actors/UpdateActorModal.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    
    it("Монтирование компоненты UpdateActorModal (isRequest: false)", async () => {
        const wrapper = getWrapper();
        
        checkContent(wrapper);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 2);
        checkInputField.checkPropsInputField(inputFields[0], 'Имя актёра:', 'text', wrapper.vm.errorsFirstName, wrapper.vm.actorFirstName, true);
        checkInputField.checkPropsInputField(inputFields[1], 'Фамилия актёра:', 'text', wrapper.vm.errorsLastName, wrapper.vm.actorLastName);
        
        checkInputField.checkInputFieldWhenThereIsNoRequest(inputFields[0], wrapper.vm.actorFirstName, 'Андрей');
        checkInputField.checkInputFieldWhenThereIsNoRequest(inputFields[1], wrapper.vm.actorLastName, 'Миронов');
        
        await checkBaseModal.hideBaseModal(wrapper, hideModal);
        await checkBaseModal.submitRequestInBaseModal(wrapper, router.put);
    });
    
    it("Монтирование компоненты UpdateActorModal (isRequest: true)", async () => {
        app.isRequest = true;
        const wrapper = getWrapper(true);
        
        checkContent(wrapper);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 2);
        checkInputField.checkPropsInputField(inputFields[0], 'Имя актёра:', 'text', wrapper.vm.errorsFirstName, wrapper.vm.actorFirstName, true);
        checkInputField.checkPropsInputField(inputFields[1], 'Фамилия актёра:', 'text', wrapper.vm.errorsLastName, wrapper.vm.actorLastName);
        
        checkInputField.checkInputFieldWhenRequestIsMade(inputFields[0], wrapper.vm.actorFirstName, 'Андрей');
        checkInputField.checkInputFieldWhenRequestIsMade(inputFields[1], wrapper.vm.actorLastName, 'Миронов');
        
        await checkBaseModal.notHideBaseModal(wrapper, hideModal);
        await checkBaseModal.notSubmitRequestInBaseModal(wrapper, router.put);
    });
    
    it("Функция handlerUpdateActor вызывает router.put с нужными параметрами", () => {
        const wrapper = getWrapper();
        const options = {
            preserveScroll: true,
            onBefore: expect.anything(),
            onSuccess: expect.anything(),
            onError: expect.anything(),
            onFinish: expect.anything()
        };
        
        wrapper.vm.handlerUpdateActor(eventCurrentTargetClassListContainsFalse);
        
        expect(router.put).toHaveBeenCalledTimes(1);
        expect(router.put).toHaveBeenCalledWith(wrapper.vm.actorsList.getUrl(actor.id), {
                first_name: wrapper.vm.actorFirstName,
                last_name: wrapper.vm.actorLastName
            }, options);
    });
    
    it("Проверка функции onBeforeForHandlerUpdateActor", () => {
        const wrapper = getWrapper();
        
        wrapper.vm.errorsFirstName = 'ErrorFirstName';
        wrapper.vm.errorsLastName = 'ErrorLastName';
        wrapper.vm.onBeforeForHandlerUpdateActor();
        
        expect(app.isRequest).toBe(true);
        expect(wrapper.vm.errorsFirstName).toBe('');
        expect(wrapper.vm.errorsLastName).toBe('');
    });
    
    it("Проверка функции onSuccessForHandlerUpdateActor", async () => {
        const wrapper = getWrapper();
        
        expect(wrapper.vm.actorsList.page).toBe(1);
        expect(hideModal).not.toHaveBeenCalled();
        wrapper.vm.onSuccessForHandlerUpdateActor({props: {actors}});
        
        expect(hideModal).toHaveBeenCalledTimes(1);
        expect(hideModal).toHaveBeenCalledWith();
        expect(wrapper.vm.actorsList.page).toBe(actors.current_page);
        expect(actors.current_page).toBe(2);
    });
    
    it("Проверка функции onErrorForHandlerUpdateActor (валидация данных)", async () => {
        const wrapper = getWrapper();
        
        expect(wrapper.vm.errorsFirstName).toBe('');
        expect(wrapper.vm.errorsLastName).toBe('');
        wrapper.vm.onErrorForHandlerUpdateActor({ first_name: 'ErrorFirstName', last_name: 'ErrorLastName' });
        
        expect(wrapper.vm.errorsFirstName).toBe('ErrorFirstName');
        expect(wrapper.vm.errorsLastName).toBe('ErrorLastName');
    });
    
    it("Проверка функции onErrorForHandlerUpdateActor (ошибка сервера с message)", async () => {
        const wrapper = getWrapper();
        
        expect(hideModal).toHaveBeenCalledTimes(0);
        wrapper.vm.onErrorForHandlerUpdateActor({ message: 'В таблице dvd.actors нет записи с id=13' });
        expect(hideModal).toHaveBeenCalledTimes(1);
    });
    
    it("Проверка функции onFinishForHandlerUpdateActor", async () => {
        app.isRequest = true;
        const wrapper = getWrapper();
        
        wrapper.vm.onFinishForHandlerUpdateActor();
        expect(app.isRequest).toBe(false);
    });
});
