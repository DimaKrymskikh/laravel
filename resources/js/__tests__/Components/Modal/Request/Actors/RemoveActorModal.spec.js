import { mount } from "@vue/test-utils";
import { router } from '@inertiajs/vue3';
import { setActivePinia, createPinia } from 'pinia';

import RemoveActorModal from '@/Components/Modal/Request/Actors/RemoveActorModal.vue';
import { useAppStore } from '@/Stores/app';
import { useActorsListStore } from '@/Stores/actors';

import { checkBaseModal } from '@/__tests__/methods/checkBaseModal';
import { checkInputField } from '@/__tests__/methods/checkInputField';
import { eventCurrentTargetClassListContainsFalse } from '@/__tests__/fake/Event';

vi.mock('@inertiajs/vue3');
        
const hideRemoveActorModal = vi.fn();

const getWrapper = function(app, actorsList) {
    return mount(RemoveActorModal, {
            props: {
                hideRemoveActorModal,
                removeActor: {
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
    expect(wrapper.vm.inputPassword).toBe('');
    expect(wrapper.vm.errorsPassword).toBe('');

    // Заголовок модального окна задаётся
    expect(wrapper.text()).toContain('Подтверждение удаления актёра');
    // Содержится вопрос модального окна с нужными параметрами
    expect(wrapper.text()).toContain('Вы действительно хотите удалить актёра');
    expect(wrapper.text()).toContain('Имя Фамилия');
};

describe("@/Components/Modal/Request/Actors/RemoveActorModal.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    
    it("Монтирование компоненты RemoveActorModal (isRequest: false)", async () => {
        const app = useAppStore();
        const actorsList = useActorsListStore();

        const wrapper = getWrapper(app, actorsList);
        
        checkContent(wrapper);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 1);
        checkInputField.checkPropsInputField(inputFields[0], 'Введите пароль:', 'password', wrapper.vm.errorsPassword, wrapper.vm.inputPassword, true);
        checkInputField.checkInputFieldWhenThereIsNoRequest(inputFields[0], wrapper.vm.inputPassword, 'TestPassword');
        
        await checkBaseModal.hideBaseModal(wrapper, hideRemoveActorModal);
        await checkBaseModal.submitRequestInBaseModal(wrapper, router.delete);
    });
    
    it("Монтирование компоненты RemoveActorModal (isRequest: true)", async () => {
        const app = useAppStore();
        app.isRequest = true;
        const actorsList = useActorsListStore();

        const wrapper = getWrapper(app);
        
        checkContent(wrapper, actorsList);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 1);
        checkInputField.checkPropsInputField(inputFields[0], 'Введите пароль:', 'password', wrapper.vm.errorsPassword, wrapper.vm.inputPassword, true);
        checkInputField.checkInputFieldWhenRequestIsMade(inputFields[0], wrapper.vm.inputPassword, 'TestPassword');
        
        await checkBaseModal.notHideBaseModal(wrapper, hideRemoveActorModal);
        await checkBaseModal.notSubmitRequestInBaseModal(wrapper, router.delete);
    });
    
    it("Функция handlerRemoveActor вызывает router.delete с нужными параметрами", () => {
        const app = useAppStore();
        const actorsList = useActorsListStore();

        const wrapper = getWrapper(app, actorsList);
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
        expect(router.delete).toHaveBeenCalledWith(actorsList.getUrl(wrapper.vm.props.removeActor.id), options);
    });
    
    it("Проверка функции onBeforeForHandlerRemoveActor", () => {
        const app = useAppStore();
        // По умолчанию
        expect(app.isRequest).toBe(false);
        const actorsList = useActorsListStore();
        
        const wrapper = getWrapper(app, actorsList);
        wrapper.vm.errorsPassword = 'ErrorPassword';
        wrapper.vm.onBeforeForHandlerRemoveActor();
        
        expect(app.isRequest).toBe(true);
        expect(wrapper.vm.errorsPassword).toBe('');
    });
    
    it("Проверка функции onSuccessForHandlerRemoveActor", async () => {
        const app = useAppStore();
        const actorsList = useActorsListStore();
        
        const wrapper = getWrapper(app, actorsList);
        
        expect(hideRemoveActorModal).not.toHaveBeenCalled();
        wrapper.vm.onSuccessForHandlerRemoveActor();
        
        expect(hideRemoveActorModal).toHaveBeenCalledTimes(1);
        expect(hideRemoveActorModal).toHaveBeenCalledWith();
    });
    
    it("Проверка функции onErrorForHandlerRemoveActor", async () => {
        const app = useAppStore();
        const actorsList = useActorsListStore();
        
        const wrapper = getWrapper(app, actorsList);
        
        expect(wrapper.vm.errorsPassword).toBe('');
        wrapper.vm.onErrorForHandlerRemoveActor({ password: 'ErrorPassword' });
        
        expect(wrapper.vm.errorsPassword).toBe('ErrorPassword');
    });
    
    it("Проверка функции onFinishForHandlerRemoveActor", async () => {
        const app = useAppStore();
        app.isRequest = true;
        const actorsList = useActorsListStore();
        
        const wrapper = getWrapper(app, actorsList);
        wrapper.vm.onFinishForHandlerRemoveActor();
        
        expect(app.isRequest).toBe(false);
    });
});
