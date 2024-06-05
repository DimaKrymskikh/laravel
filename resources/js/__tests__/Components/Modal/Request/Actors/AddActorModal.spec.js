import { mount } from "@vue/test-utils";
import { router } from '@inertiajs/vue3';

import { setActivePinia, createPinia } from 'pinia';
import AddActorModal from '@/Components/Modal/Request/Actors/AddActorModal.vue';
import { useAppStore } from '@/Stores/app';
import { useActorsListStore } from '@/Stores/actors';

import { checkBaseModal } from '@/__tests__/methods/checkBaseModal';
import { checkInputField } from '@/__tests__/methods/checkInputField';
import { eventCurrentTargetClassListContainsFalse } from '@/__tests__/fake/Event';
import { actors } from '@/__tests__/data/actors';

vi.mock('@inertiajs/vue3');
        
const hideAddActorModal = vi.fn();

const getWrapper = function(app, actorsList) {
    return mount(AddActorModal, {
            props: {
                hideAddActorModal
            },
            global: {
                provide: { app, actorsList }
            }
        });
};

const checkContent = function(wrapper) {
    // Проверка равенства переменных ref начальным данным
    expect(wrapper.vm.firstName).toBe('');
    expect(wrapper.vm.lastName).toBe('');
    expect(wrapper.vm.errorsFirstName).toBe('');
    expect(wrapper.vm.errorsLastName).toBe('');

    // Заголовок модального окна задаётся
    expect(wrapper.text()).toContain('Добавление актёра');
};
        
describe("@/Components/Modal/Request/Actors/AddActorModal.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    
    it("Монтирование компоненты AddCityModal (isRequest: false)", async () => {
        const app = useAppStore();
        const actorsList = useActorsListStore();

        const wrapper = getWrapper(app, actorsList);
        
        checkContent (wrapper);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 2);
        checkInputField.checkPropsInputField(inputFields[0], 'Имя актёра:', 'text', wrapper.vm.errorsFirstName, wrapper.vm.firstName, true);
        checkInputField.checkPropsInputField(inputFields[1], 'Фамилия актёра:', 'text', wrapper.vm.errorsLastName, wrapper.vm.lastName);
        
        checkInputField.checkInputFieldWhenThereIsNoRequest(inputFields[0], wrapper.vm.firstName, 'Имя');
        checkInputField.checkInputFieldWhenThereIsNoRequest(inputFields[1], wrapper.vm.lastName, 'Фамилия');
        
        await checkBaseModal.hideBaseModal(wrapper, hideAddActorModal);
        await checkBaseModal.submitRequestInBaseModal(wrapper, router.post);
    });
    
    it("Монтирование компоненты AddCityModal (isRequest: true)", async () => {
        const app = useAppStore();
        app.isRequest = true;
        const actorsList = useActorsListStore();

        const wrapper = getWrapper(app, actorsList);
        
        checkContent (wrapper);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 2);
        checkInputField.checkPropsInputField(inputFields[0], 'Имя актёра:', 'text', wrapper.vm.errorsFirstName, wrapper.vm.firstName, true);
        checkInputField.checkPropsInputField(inputFields[1], 'Фамилия актёра:', 'text', wrapper.vm.errorsLastName, wrapper.vm.lastName);
        
        checkInputField.checkInputFieldWhenRequestIsMade(inputFields[0], wrapper.vm.firstName, 'Имя');
        checkInputField.checkInputFieldWhenRequestIsMade(inputFields[1], wrapper.vm.lastName, 'Фамилия');
        
        await checkBaseModal.notHideBaseModal(wrapper, hideAddActorModal);
        await checkBaseModal.notSubmitRequestInBaseModal(wrapper, router.post);
    });
    
    it("Функция handlerAddActor вызывает router.post с нужными параметрами", () => {
        const app = useAppStore();
        const actorsList = useActorsListStore();
        const options = {
            onBefore: expect.anything(),
            onSuccess: expect.anything(),
            onError: expect.anything(),
            onFinish: expect.anything()
        };

        const wrapper = getWrapper(app, actorsList);
        
        wrapper.vm.handlerAddActor(eventCurrentTargetClassListContainsFalse);
        
        expect(router.post).toHaveBeenCalledTimes(1);
        expect(router.post).toHaveBeenCalledWith(actorsList.getUrl(), {
            first_name: wrapper.vm.firstName,
            last_name: wrapper.vm.lastName
        }, options);
    });
    
    it("Проверка функции onBeforeForHandlerAddActor", () => {
        const app = useAppStore();
        // По умолчанию
        expect(app.isRequest).toBe(false);
        const actorsList = useActorsListStore();
        
        const wrapper = getWrapper(app, actorsList);
        wrapper.vm.errorsFirstName = 'ErrorFirstName';
        wrapper.vm.errorsLastName = 'ErrorLastName';
        wrapper.vm.onBeforeForHandlerAddActor();
        
        expect(app.isRequest).toBe(true);
        expect(wrapper.vm.errorsFirstName).toBe('');
        expect(wrapper.vm.errorsLastName).toBe('');
    });
    
    it("Проверка функции onSuccessForHandlerAddActor", async () => {
        const app = useAppStore();
        const actorsList = useActorsListStore();
        expect(actorsList.page).toBe(1);
        
        const wrapper = getWrapper(app, actorsList);
        
        expect(hideAddActorModal).not.toHaveBeenCalled();
        wrapper.vm.onSuccessForHandlerAddActor({props: {actors}});
        
        expect(hideAddActorModal).toHaveBeenCalledTimes(1);
        expect(hideAddActorModal).toHaveBeenCalledWith();
        expect(actorsList.page).toBe(actors.current_page);
        expect(actors.current_page).toBe(2);
    });
    
    it("Проверка функции onErrorForHandlerAddActor", async () => {
        const app = useAppStore();
        const actorsList = useActorsListStore();
        
        const wrapper = getWrapper(app, actorsList);
        
        expect(wrapper.vm.errorsFirstName).toBe('');
        expect(wrapper.vm.errorsLastName).toBe('');
        wrapper.vm.onErrorForHandlerAddActor({ first_name: 'ErrorFirstName', last_name: 'ErrorLastName' });
        
        expect(wrapper.vm.errorsFirstName).toBe('ErrorFirstName');
        expect(wrapper.vm.errorsLastName).toBe('ErrorLastName');
    });
    
    it("Проверка функции onFinishForHandlerAddActor", async () => {
        const app = useAppStore();
        app.isRequest = true;
        const actorsList = useActorsListStore();
        
        const wrapper = getWrapper(app, actorsList);
        wrapper.vm.onFinishForHandlerAddActor();
        
        expect(app.isRequest).toBe(false);
    });
});
