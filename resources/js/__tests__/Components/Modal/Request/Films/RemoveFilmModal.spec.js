import { mount } from "@vue/test-utils";
import { router } from '@inertiajs/vue3';

import { setActivePinia, createPinia } from 'pinia';
import RemoveFilmModal from '@/Components/Modal/Request/Films/RemoveFilmModal.vue';
import { useAppStore } from '@/Stores/app';
import { useFilmsAdminStore } from '@/Stores/films';

import { checkBaseModal } from '@/__tests__/methods/checkBaseModal';
import { checkInputField } from '@/__tests__/methods/checkInputField';

vi.mock('@inertiajs/vue3');
        
const hideRemoveFilmModal = vi.fn();
const removeFilm = {
    id: 19,
    title: 'Бриллиантовая рука',
    fieldValue: ''
};

const getWrapper = function(app, filmsAdmin) {
    return mount(RemoveFilmModal, {
            props: {
                hideRemoveFilmModal,
                removeFilm
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
    expect(wrapper.text()).toContain('Подтверждение удаления фильма');
    // Содержится вопрос модального окна с нужными параметрами
    expect(wrapper.text()).toContain(`Вы действительно хотите удалить фильм ${removeFilm.title}`);
};

describe("@/Components/Modal/Request/Films/RemoveFilmModal.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    
    it("Монтирование компоненты RemoveFilmModal (isRequest: false)", async () => {
        const app = useAppStore();
        const filmsAdmin = useFilmsAdminStore();

        const wrapper = getWrapper(app, filmsAdmin);
        
        checkContent(wrapper);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 1);
        checkInputField.checkPropsInputField(inputFields[0], 'Введите пароль:', 'password', wrapper.vm.errorsPassword, wrapper.vm.inputPassword, true);
        checkInputField.checkInputFieldWhenThereIsNoRequest(inputFields[0], wrapper.vm.inputPassword, 'TestPassword');
        
        await checkBaseModal.hideBaseModal(wrapper, hideRemoveFilmModal);
        await checkBaseModal.submitRequestInBaseModal(wrapper, router.delete);
    });
    
    it("Монтирование компоненты RemoveFilmModal (isRequest: true)", async () => {
        const app = useAppStore();
        app.isRequest = true;
        const filmsAdmin = useFilmsAdminStore();

        const wrapper = getWrapper(app, filmsAdmin);
        
        checkContent(wrapper);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 1);
        checkInputField.checkPropsInputField(inputFields[0], 'Введите пароль:', 'password', wrapper.vm.errorsPassword, wrapper.vm.inputPassword, true);
        checkInputField.checkInputFieldWhenRequestIsMade(inputFields[0], wrapper.vm.inputPassword, 'TestPassword');
        
        await checkBaseModal.notHideBaseModal(wrapper, hideRemoveFilmModal);
        await checkBaseModal.notSubmitRequestInBaseModal(wrapper, router.delete);
    });
});
