import { mount } from "@vue/test-utils";
import { router } from '@inertiajs/vue3';

import { setActivePinia, createPinia } from 'pinia';
import AddFilmModal from '@/Components/Modal/Request/Films/AddFilmModal.vue';
import { useAppStore } from '@/Stores/app';
import { useFilmsAdminStore } from '@/Stores/films';

import { checkBaseModal } from '@/__tests__/methods/checkBaseModal';
import { checkInputField } from '@/__tests__/methods/checkInputField';

vi.mock('@inertiajs/vue3');
        
const hideAddFilmModal = vi.fn();

const getWrapper = function(app, filmsAdmin) {
    return mount(AddFilmModal, {
            props: {
                hideAddFilmModal
            },
            global: {
                provide: { app, filmsAdmin }
            }
        });
};

const checkContent = function(wrapper) {
    // Проверка равенства переменных ref начальным данным
    expect(wrapper.vm.title).toBe('');
    expect(wrapper.vm.description).toBe('');
    expect(wrapper.vm.errorsTitle).toBe('');
    expect(wrapper.vm.errorsDescription).toBe('');

    // Заголовок модального окна задаётся
    expect(wrapper.text()).toContain('Добавление фильма');
};
        
describe("@/Components/Modal/Request/Films/AddFilmModal.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    
    it("Монтирование компоненты AddFilmModal (isRequest: false)", async () => {
        const app = useAppStore();
        const filmsAdmin = useFilmsAdminStore();

        const wrapper = getWrapper(app, filmsAdmin);
        
        checkContent (wrapper);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 2);
        checkInputField.checkPropsInputField(inputFields[0], 'Название фильма:', 'text', wrapper.vm.errorsTitle, wrapper.vm.title, true);
        checkInputField.checkPropsInputField(inputFields[1], 'Описание фильма:', 'text', wrapper.vm.errorsDescription, wrapper.vm.description);
        
        checkInputField.checkInputFieldWhenThereIsNoRequest(inputFields[0], wrapper.vm.title, 'Имя фильма');
        checkInputField.checkInputFieldWhenThereIsNoRequest(inputFields[1], wrapper.vm.description, 'Некоторое описание фильма');
        
        await checkBaseModal.hideBaseModal(wrapper, hideAddFilmModal);
        await checkBaseModal.submitRequestInBaseModal(wrapper, router.post);
    });
    
    it("Монтирование компоненты AddFilmModal (isRequest: true)", async () => {
        const app = useAppStore();
        app.isRequest = true;
        const filmsAdmin = useFilmsAdminStore();

        const wrapper = getWrapper(app, filmsAdmin);
        
        checkContent (wrapper);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 2);
        checkInputField.checkPropsInputField(inputFields[0], 'Название фильма:', 'text', wrapper.vm.errorsTitle, wrapper.vm.title, true);
        checkInputField.checkPropsInputField(inputFields[1], 'Описание фильма:', 'text', wrapper.vm.errorsDescription, wrapper.vm.description);
        
        checkInputField.checkInputFieldWhenRequestIsMade(inputFields[0], wrapper.vm.title, 'Имя фильма');
        checkInputField.checkInputFieldWhenRequestIsMade(inputFields[1], wrapper.vm.description, 'Некоторое описание фильма');
        
        await checkBaseModal.notHideBaseModal(wrapper, hideAddFilmModal);
        await checkBaseModal.notSubmitRequestInBaseModal(wrapper, router.post);
    });
});
