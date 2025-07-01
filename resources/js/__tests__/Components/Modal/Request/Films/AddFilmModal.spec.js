import { mount, flushPromises } from "@vue/test-utils";
import { router } from '@inertiajs/vue3';

import { setActivePinia, createPinia } from 'pinia';
import { app } from '@/Services/app';
import { film } from '@/Services/Content/films';
import AddFilmModal from '@/Components/Modal/Request/Films/AddFilmModal.vue';
import { useFilmsAdminStore } from '@/Stores/films';

import { checkBaseModal } from '@/__tests__/methods/checkBaseModal';
import { checkInputField } from '@/__tests__/methods/checkInputField';
import { films_10 } from '@/__tests__/data/films';
import { eventCurrentTargetClassListContainsFalse } from '@/__tests__/fake/Event';

vi.mock('@inertiajs/vue3');
        
const hideModal = vi.spyOn(film, 'hideAddFilmModal');

const getWrapper = function() {
    return mount(AddFilmModal, {
            global: {
                provide: {
                    filmsAdmin: useFilmsAdminStore()
                }
            }
        });
};

const checkContent = function(wrapper) {
    // Проверка равенства переменных ref начальным данным
    expect(wrapper.vm.title).toBe(film.title);
    expect(wrapper.vm.description).toBe(film.description);
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
        const wrapper = getWrapper();
        
        checkContent (wrapper);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 2);
        checkInputField.checkPropsInputField(inputFields[0], 'Название фильма:', 'text', wrapper.vm.errorsTitle, wrapper.vm.title, true);
        checkInputField.checkPropsInputField(inputFields[1], 'Описание фильма:', 'text', wrapper.vm.errorsDescription, wrapper.vm.description);
        
        checkInputField.checkInputFieldWhenThereIsNoRequest(inputFields[0], wrapper.vm.title, 'Имя фильма');
        checkInputField.checkInputFieldWhenThereIsNoRequest(inputFields[1], wrapper.vm.description, 'Некоторое описание фильма');
        
        await checkBaseModal.hideBaseModal(wrapper, hideModal);
        await checkBaseModal.submitRequestInBaseModal(wrapper, router.post);
    });
    
    it("Монтирование компоненты AddFilmModal (isRequest: true)", async () => {
        app.isRequest = true;
        const wrapper = getWrapper();
        await flushPromises();
        
        checkContent (wrapper);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 2);
        checkInputField.checkPropsInputField(inputFields[0], 'Название фильма:', 'text', wrapper.vm.errorsTitle, wrapper.vm.title, true);
        checkInputField.checkPropsInputField(inputFields[1], 'Описание фильма:', 'text', wrapper.vm.errorsDescription, wrapper.vm.description);
        
        checkInputField.checkInputFieldWhenRequestIsMade(inputFields[0], wrapper.vm.title, 'Имя фильма');
        checkInputField.checkInputFieldWhenRequestIsMade(inputFields[1], wrapper.vm.description, 'Некоторое описание фильма');
        
        await checkBaseModal.notHideBaseModal(wrapper, hideModal);
        await checkBaseModal.notSubmitRequestInBaseModal(wrapper, router.post);
    });
    
    it("Функция handlerAddFilm вызывает router.post с нужными параметрами", () => {
        const wrapper = getWrapper();
        
        const options = {
            onBefore: expect.anything(),
            onSuccess: expect.anything(),
            onError: expect.anything(),
            onFinish: expect.anything()
        };

        wrapper.vm.handlerAddFilm(eventCurrentTargetClassListContainsFalse);
        
        expect(router.post).toHaveBeenCalledTimes(1);
        expect(router.post).toHaveBeenCalledWith(wrapper.vm.filmsAdmin.getUrl('/admin/films'), {
            title: wrapper.vm.title,
            description: wrapper.vm.description
        }, options);
    });
    
    it("Проверка функции onBeforeForHandlerAddFilm", () => {
        const wrapper = getWrapper();
        
        wrapper.vm.errorsTitle = 'ErrorTitle';
        wrapper.vm.errorsDescription = 'ErrorDescription';
        wrapper.vm.onBeforeForHandlerAddFilm();
        
        expect(app.isRequest).toBe(true);
        expect(wrapper.vm.errorsTitle).toBe('');
        expect(wrapper.vm.errorsDescription).toBe('');
    });
    
    it("Проверка функции onSuccessForHandlerAddFilm", async () => {
        const wrapper = getWrapper();
        
        wrapper.vm.filmsAdmin.title = 'Title';
        wrapper.vm.filmsAdmin.description = 'Description';
        wrapper.vm.filmsAdmin.release_year = 2001;
        expect(wrapper.vm.filmsAdmin.page).toBe(1);
        await flushPromises();
        
        expect(hideModal).not.toHaveBeenCalled();
        wrapper.vm.onSuccessForHandlerAddFilm({
            props: {
                films: films_10
            }
        });
        
        expect(hideModal).toHaveBeenCalledTimes(1);
        expect(hideModal).toHaveBeenCalledWith();
        expect(wrapper.vm.filmsAdmin.title).toBe('');
        expect(wrapper.vm.filmsAdmin.description).toBe('');
        expect(wrapper.vm.filmsAdmin.releaseYear).toBe('');
        expect(wrapper.vm.filmsAdmin.page).toBe(films_10.current_page);
    });
    
    it("Проверка функции onErrorForHandlerAddFilm", async () => {
        const wrapper = getWrapper();
        
        expect(wrapper.vm.errorsTitle).toBe('');
        expect(wrapper.vm.errorsDescription).toBe('');
        wrapper.vm.onErrorForHandlerAddFilm({title: 'ErrorTitle', description: 'ErrorDescription'});
        
        expect(wrapper.vm.errorsTitle).toBe('ErrorTitle');
        expect(wrapper.vm.errorsDescription).toBe('ErrorDescription');
    });
    
    it("Проверка функции onFinishForHandlerAddFilm", async () => {
        app.isRequest = true;
        const wrapper = getWrapper();
        await flushPromises();
        
        wrapper.vm.onFinishForHandlerAddFilm();
        expect(app.isRequest).toBe(false);
    });
});
