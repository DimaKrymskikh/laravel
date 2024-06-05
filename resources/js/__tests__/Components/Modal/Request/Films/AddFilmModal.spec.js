import { mount } from "@vue/test-utils";
import { router } from '@inertiajs/vue3';

import { setActivePinia, createPinia } from 'pinia';
import AddFilmModal from '@/Components/Modal/Request/Films/AddFilmModal.vue';
import { useAppStore } from '@/Stores/app';
import { useFilmsAdminStore } from '@/Stores/films';

import { checkBaseModal } from '@/__tests__/methods/checkBaseModal';
import { checkInputField } from '@/__tests__/methods/checkInputField';
import { films_10 } from '@/__tests__/data/films';
import { eventCurrentTargetClassListContainsFalse } from '@/__tests__/fake/Event';

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
    
    it("Функция handlerAddFilm вызывает router.post с нужными параметрами", () => {
        const app = useAppStore();
        const filmsAdmin = useFilmsAdminStore();
        const options = {
            onBefore: expect.anything(),
            onSuccess: expect.anything(),
            onError: expect.anything(),
            onFinish: expect.anything()
        };

        const wrapper = getWrapper(app, filmsAdmin);
        
        wrapper.vm.handlerAddFilm(eventCurrentTargetClassListContainsFalse);
        
        expect(router.post).toHaveBeenCalledTimes(1);
        expect(router.post).toHaveBeenCalledWith(filmsAdmin.getUrl('/admin/films'), {
            title: wrapper.vm.title,
            description: wrapper.vm.description
        }, options);
    });
    
    it("Проверка функции onBeforeForHandlerAddFilm", () => {
        const app = useAppStore();
        // По умолчанию
        expect(app.isRequest).toBe(false);
        const filmsAdmin = useFilmsAdminStore();
        
        const wrapper = getWrapper(app, filmsAdmin);
        wrapper.vm.errorsTitle = 'ErrorTitle';
        wrapper.vm.errorsDescription = 'ErrorDescription';
        wrapper.vm.onBeforeForHandlerAddFilm();
        
        expect(app.isRequest).toBe(true);
        expect(wrapper.vm.errorsTitle).toBe('');
        expect(wrapper.vm.errorsDescription).toBe('');
    });
    
    it("Проверка функции onSuccessForHandlerAddFilm", async () => {
        const app = useAppStore();
        const filmsAdmin = useFilmsAdminStore();
        filmsAdmin.title = 'Title';
        filmsAdmin.description = 'Description';
        filmsAdmin.release_year = 2001;
        expect(filmsAdmin.page).toBe(1);
        
        const wrapper = getWrapper(app, filmsAdmin);
        
        expect(hideAddFilmModal).not.toHaveBeenCalled();
        wrapper.vm.onSuccessForHandlerAddFilm({
            props: {
                films: films_10
            }
        });
        
        expect(hideAddFilmModal).toHaveBeenCalledTimes(1);
        expect(hideAddFilmModal).toHaveBeenCalledWith();
        expect(filmsAdmin.title).toBe('');
        expect(filmsAdmin.description).toBe('');
        expect(filmsAdmin.release_year).toBe('');
        expect(filmsAdmin.page).toBe(films_10.current_page);
    });
    
    it("Проверка функции onErrorForHandlerAddFilm", async () => {
        const app = useAppStore();
        const filmsAdmin = useFilmsAdminStore();
        
        const wrapper = getWrapper(app, filmsAdmin);
        
        expect(wrapper.vm.errorsTitle).toBe('');
        expect(wrapper.vm.errorsDescription).toBe('');
        wrapper.vm.onErrorForHandlerAddFilm({title: 'ErrorTitle', description: 'ErrorDescription'});
        
        expect(wrapper.vm.errorsTitle).toBe('ErrorTitle');
        expect(wrapper.vm.errorsDescription).toBe('ErrorDescription');
    });
    
    it("Проверка функции onFinishForHandlerAddFilm", async () => {
        const app = useAppStore();
        app.isRequest = true;
        const filmsAdmin = useFilmsAdminStore();
        
        const wrapper = getWrapper(app, filmsAdmin);
        wrapper.vm.onFinishForHandlerAddFilm();
        
        expect(app.isRequest).toBe(false);
    });
});
