import { mount } from "@vue/test-utils";
import { router } from '@inertiajs/vue3';

import { setActivePinia, createPinia } from 'pinia';
import UpdateActorModal from '@/Components/Modal/Request/Actors/UpdateActorModal.vue';
import { useAppStore } from '@/Stores/app';
import { useActorsListStore } from '@/Stores/actors';

import { checkBaseModal } from '@/__tests__/methods/checkBaseModal';
import { checkInputField } from '@/__tests__/methods/checkInputField';
import { eventCurrentTargetClassListContainsFalse } from '@/__tests__/fake/Event';
import { actors } from '@/__tests__/data/actors';

vi.mock('@inertiajs/vue3');
        
const hideUpdateActorModal = vi.fn();

const getWrapper = function(app, actorsList) {
    return mount(UpdateActorModal, {
            props: {
                hideUpdateActorModal,
                updateActor: {
                    id: '5',
                    first_name: 'Имя',
                    last_name: 'Фамилия'
                }
            },
            global: {
                provide: { app, actorsList }
            }
        });
};

const checkContent = function(wrapper) {
    // Проверка равенства переменных ref начальным данным
    expect(wrapper.vm.actorFirstName).toBe('Имя');
    expect(wrapper.vm.actorLastName).toBe('Фамилия');
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
        const app = useAppStore();
        const actorsList = useActorsListStore();

        const wrapper = getWrapper(app, actorsList);
        
        checkContent(wrapper);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 2);
        checkInputField.checkPropsInputField(inputFields[0], 'Имя актёра:', 'text', wrapper.vm.errorsFirstName, wrapper.vm.actorFirstName, true);
        checkInputField.checkPropsInputField(inputFields[1], 'Фамилия актёра:', 'text', wrapper.vm.errorsLastName, wrapper.vm.actorLastName);
        
        checkInputField.checkInputFieldWhenThereIsNoRequest(inputFields[0], wrapper.vm.actorFirstName, 'Андрей');
        checkInputField.checkInputFieldWhenThereIsNoRequest(inputFields[1], wrapper.vm.actorLastName, 'Миронов');
        
        await checkBaseModal.hideBaseModal(wrapper, hideUpdateActorModal);
        await checkBaseModal.submitRequestInBaseModal(wrapper, router.put);
    });
    
    it("Монтирование компоненты UpdateActorModal (isRequest: true)", async () => {
        const app = useAppStore();
        app.isRequest = true;
        const actorsList = useActorsListStore();

        const wrapper = getWrapper(app, actorsList);
        
        checkContent(wrapper);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 2);
        checkInputField.checkPropsInputField(inputFields[0], 'Имя актёра:', 'text', wrapper.vm.errorsFirstName, wrapper.vm.actorFirstName, true);
        checkInputField.checkPropsInputField(inputFields[1], 'Фамилия актёра:', 'text', wrapper.vm.errorsLastName, wrapper.vm.actorLastName);
        
        checkInputField.checkInputFieldWhenRequestIsMade(inputFields[0], wrapper.vm.actorFirstName, 'Андрей');
        checkInputField.checkInputFieldWhenRequestIsMade(inputFields[1], wrapper.vm.actorLastName, 'Миронов');
        
        await checkBaseModal.notHideBaseModal(wrapper, hideUpdateActorModal);
        await checkBaseModal.notSubmitRequestInBaseModal(wrapper, router.put);
    });
    
    it("Функция handlerUpdateActor вызывает router.put с нужными параметрами", () => {
        const app = useAppStore();
        const actorsList = useActorsListStore();
        const options = {
            preserveScroll: true,
            onBefore: expect.anything(),
            onSuccess: expect.anything(),
            onError: expect.anything(),
            onFinish: expect.anything()
        };

        const wrapper = getWrapper(app, actorsList);
        
        wrapper.vm.handlerUpdateActor(eventCurrentTargetClassListContainsFalse);
        
        expect(router.put).toHaveBeenCalledTimes(1);
        expect(router.put).toHaveBeenCalledWith(actorsList.getUrl(wrapper.vm.props.updateActor.id), {
                first_name: wrapper.vm.actorFirstName,
                last_name: wrapper.vm.actorLastName
            }, options);
    });
    
    it("Проверка функции onBeforeForHandlerUpdateActor", () => {
        const app = useAppStore();
        // По умолчанию
        expect(app.isRequest).toBe(false);
        const actorsList = useActorsListStore();
        
        const wrapper = getWrapper(app, actorsList);
        wrapper.vm.errorsFirstName = 'ErrorFirstName';
        wrapper.vm.errorsLastName = 'ErrorLastName';
        wrapper.vm.onBeforeForHandlerUpdateActor();
        
        expect(app.isRequest).toBe(true);
        expect(wrapper.vm.errorsFirstName).toBe('');
        expect(wrapper.vm.errorsLastName).toBe('');
    });
    
    it("Проверка функции onSuccessForHandlerUpdateActor", async () => {
        const app = useAppStore();
        const actorsList = useActorsListStore();
        expect(actorsList.page).toBe(1);
        
        const wrapper = getWrapper(app, actorsList);
        
        expect(hideUpdateActorModal).not.toHaveBeenCalled();
        wrapper.vm.onSuccessForHandlerUpdateActor({props: {actors}});
        
        expect(hideUpdateActorModal).toHaveBeenCalledTimes(1);
        expect(hideUpdateActorModal).toHaveBeenCalledWith();
        expect(actorsList.page).toBe(actors.current_page);
        expect(actors.current_page).toBe(2);
    });
    
    it("Проверка функции onErrorForHandlerUpdateActor", async () => {
        const app = useAppStore();
        const actorsList = useActorsListStore();
        
        const wrapper = getWrapper(app, actorsList);
        
        expect(wrapper.vm.errorsFirstName).toBe('');
        expect(wrapper.vm.errorsLastName).toBe('');
        wrapper.vm.onErrorForHandlerUpdateActor({ first_name: 'ErrorFirstName', last_name: 'ErrorLastName' });
        
        expect(wrapper.vm.errorsFirstName).toBe('ErrorFirstName');
        expect(wrapper.vm.errorsLastName).toBe('ErrorLastName');
    });
    
    it("Проверка функции onFinishForHandlerUpdateActor", async () => {
        const app = useAppStore();
        app.isRequest = true;
        const actorsList = useActorsListStore();
        
        const wrapper = getWrapper(app, actorsList);
        wrapper.vm.onFinishForHandlerUpdateActor();
        
        expect(app.isRequest).toBe(false);
    });
});
