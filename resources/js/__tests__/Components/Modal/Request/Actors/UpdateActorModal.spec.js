import { mount } from "@vue/test-utils";
import { router } from '@inertiajs/vue3';

import { setActivePinia, createPinia } from 'pinia';
import UpdateActorModal from '@/Components/Modal/Request/Actors/UpdateActorModal.vue';
import { useAppStore } from '@/Stores/app';
import { useActorsListStore } from '@/Stores/actors';

import { checkBaseModal } from '@/__tests__/methods/checkBaseModal';
import { checkInputField } from '@/__tests__/methods/checkInputField';

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
});
