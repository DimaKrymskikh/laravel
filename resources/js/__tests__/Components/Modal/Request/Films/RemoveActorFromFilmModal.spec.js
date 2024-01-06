import { mount } from "@vue/test-utils";
import { router } from '@inertiajs/vue3';

import { setActivePinia, createPinia } from 'pinia';
import RemoveActorFromFilmModal from '@/Components/Modal/Request/Films/RemoveActorFromFilmModal.vue';
import { useAppStore } from '@/Stores/app';
import { useFilmsAdminStore } from '@/Stores/films';

import { checkBaseModal } from '@/__tests__/methods/checkBaseModal';
import { checkInputField } from '@/__tests__/methods/checkInputField';

vi.mock('@inertiajs/vue3');
        
const hideRemoveActorFromFilmModal = vi.fn();
const updateFilm = {
    id: 19,
    title: 'Бриллиантовая рука',
    fieldValue: ''
};
const removeActor = {
    id: 7,
    first_name: 'Андрей',
    last_name: 'Миронов'
};

const getWrapper = function(app, filmsAdmin) {
    return mount(RemoveActorFromFilmModal, {
            props: {
                hideRemoveActorFromFilmModal,
                updateFilm,
                removeActor
            },
            global: {
                provide: { app, filmsAdmin }
            }
        });
};

const checkContent = function(wrapper) {
    // Проверка равенства переменных ref начальным данным
    expect(wrapper.vm.inputPassword).toBe('');
    expect(wrapper.vm.errorsPassword).toBe('');

    // Заголовок модального окна задаётся
    expect(wrapper.text()).toContain('Подтверждение удаления актёра из фильма');
    // Содержится вопрос модального окна с нужными параметрами
    expect(wrapper.text()).toContain(`Вы действительно хотите удалить актёра ${removeActor.first_name} ${removeActor.last_name}`);
    expect(wrapper.text()).toContain(`из фильма ${updateFilm.title}`);
};

describe("@/Components/Modal/Request/Films/RemoveActorFromFilmModal.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    
    it("Монтирование компоненты RemoveActorFromFilmModal (isRequest: false)", async () => {
        const app = useAppStore();
        const filmsAdmin = useFilmsAdminStore();

        const wrapper = getWrapper(app, filmsAdmin);
        
        checkContent(wrapper);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 1);
        checkInputField.checkPropsInputField(inputFields[0], 'Введите пароль:', 'password', wrapper.vm.errorsPassword, wrapper.vm.inputPassword, true);
        checkInputField.checkInputFieldWhenThereIsNoRequest(inputFields[0], wrapper.vm.inputPassword, 'TestPassword');
        
        await checkBaseModal.hideBaseModal(wrapper, hideRemoveActorFromFilmModal);
        await checkBaseModal.submitRequestInBaseModal(wrapper, router.delete);
    });
    
    it("Монтирование компоненты RemoveActorFromFilmModal (isRequest: true)", async () => {
        const app = useAppStore();
        app.isRequest = true;
        const filmsAdmin = useFilmsAdminStore();

        const wrapper = getWrapper(app, filmsAdmin);
        
        checkContent(wrapper);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 1);
        checkInputField.checkPropsInputField(inputFields[0], 'Введите пароль:', 'password', wrapper.vm.errorsPassword, wrapper.vm.inputPassword, true);
        checkInputField.checkInputFieldWhenRequestIsMade(inputFields[0], wrapper.vm.inputPassword, 'TestPassword');
        
        await checkBaseModal.notHideBaseModal(wrapper, hideRemoveActorFromFilmModal);
        await checkBaseModal.notSubmitRequestInBaseModal(wrapper, router.delete);
    });
});
