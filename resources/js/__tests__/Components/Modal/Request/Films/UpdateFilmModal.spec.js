import { mount, flushPromises } from "@vue/test-utils";
import { router } from '@inertiajs/vue3';

import { setActivePinia, createPinia } from 'pinia';
import { app } from '@/Services/app';
import { film } from '@/Services/Content/films';
import UpdateFilmModal from '@/Components/Modal/Request/Films/UpdateFilmModal.vue';
import { useFilmsAdminStore } from '@/Stores/films';

import { checkBaseModal } from '@/__tests__/methods/checkBaseModal';
import { checkInputField } from '@/__tests__/methods/checkInputField';
import { eventCurrentTargetClassListContainsFalse } from '@/__tests__/fake/Event';

vi.mock('@inertiajs/vue3');
        
const hideModal = vi.spyOn(film, 'hideUpdateFilmModal');

function setUpdateFilm(field, fieldValue) {
    film.id = 8;
    film.title = 'Бриллиантовая рука';
    film.field = field;
    film.fieldValue = fieldValue;
};

const getWrapper = function() {
    return mount(UpdateFilmModal, {
            global: {
                provide: {
                    filmsAdmin: useFilmsAdminStore()
                }
            }
        });
};

const checkContent = function(wrapper) {
    // Проверка равенства переменных ref начальным данным
    expect(wrapper.vm.fieldValue).toBe(film.fieldValue);
    expect(wrapper.vm.errorsField).toBe('');

    // Заголовок модального окна и имя поля задаются
    expect(wrapper.text()).toContain(wrapper.vm.headerTitle);
    expect(wrapper.text()).toContain(wrapper.vm.titleText);
};

describe("@/Components/Modal/Request/Films/UpdateFilmModal.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    
    it("Монтирование компоненты UpdateFilmModal (isRequest: false, field: title)", async () => {
        setUpdateFilm('title', '');

        const wrapper = getWrapper();
        
        checkContent(wrapper);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 1);
        checkInputField.checkPropsInputField(inputFields[0], wrapper.vm.titleText, 'text', wrapper.vm.errorsField, wrapper.vm.fieldValue, true);
        checkInputField.checkInputFieldWhenThereIsNoRequest(inputFields[0], wrapper.vm.fieldValue, 'Бриллиантовая нога');
        
        await checkBaseModal.hideBaseModal(wrapper, hideModal);
        await checkBaseModal.submitRequestInBaseModal(wrapper, router.put);
    });
    
    it("Монтирование компоненты UpdateFilmModal (isRequest: false, field: description)", async () => {
        setUpdateFilm('description', '');

        const wrapper = getWrapper();
        
        checkContent(wrapper);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 1);
        checkInputField.checkPropsInputField(inputFields[0], wrapper.vm.titleText, 'text', wrapper.vm.errorsField, wrapper.vm.fieldValue, true);
        checkInputField.checkInputFieldWhenThereIsNoRequest(inputFields[0], wrapper.vm.fieldValue, 'Жулики хотят вернуть себе бриллианты любой ценой');
        
        await checkBaseModal.hideBaseModal(wrapper, hideModal);
        await checkBaseModal.submitRequestInBaseModal(wrapper, router.put);
    });
    
    it("Монтирование компоненты UpdateFilmModal (isRequest: false, field: release_year)", async () => {
        setUpdateFilm('release_year', '');

        const wrapper = getWrapper();
        
        checkContent(wrapper);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 1);
        checkInputField.checkPropsInputField(inputFields[0], wrapper.vm.titleText, 'text', wrapper.vm.errorsField, wrapper.vm.fieldValue, true);
        checkInputField.checkInputFieldWhenThereIsNoRequest(inputFields[0], wrapper.vm.fieldValue, '1970');
        
        await checkBaseModal.hideBaseModal(wrapper, hideModal);
        await checkBaseModal.submitRequestInBaseModal(wrapper, router.put);
    });
    
    it("Монтирование компоненты UpdateFilmModal (isRequest: true)", async () => {
        setUpdateFilm('description', '');

        app.isRequest = true;
        const wrapper = getWrapper();
        await flushPromises();
        
        checkContent(wrapper);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 1);
        checkInputField.checkPropsInputField(inputFields[0], wrapper.vm.titleText, 'text', wrapper.vm.errorsField, wrapper.vm.fieldValue, true);
        checkInputField.checkInputFieldWhenRequestIsMade(inputFields[0], wrapper.vm.fieldValue, 'Жулики хотят вернуть себе бриллианты любой ценой');
        
        await checkBaseModal.notHideBaseModal(wrapper, hideModal);
        await checkBaseModal.notSubmitRequestInBaseModal(wrapper, router.put);
    });
    
    it("Функция handlerUpdateFilm вызывает router.put с нужными параметрами", () => {
        const options = {
            onBefore: expect.anything(),
            onSuccess: expect.anything(),
            onError: expect.anything(),
            onFinish: expect.anything()
        };
        
        setUpdateFilm('description', '');

        const wrapper = getWrapper();
        
        wrapper.vm.handlerUpdateFilm(eventCurrentTargetClassListContainsFalse);
        
        expect(router.put).toHaveBeenCalledTimes(1);
        expect(router.put).toHaveBeenCalledWith(wrapper.vm.filmsAdmin.getUrl(`/admin/films/${film.id}`), {
                field: film.field,
                [film.field]: wrapper.vm.fieldValue
            }, options);
    });
    
    it("Проверка функции onBeforeForHandlerUpdateFilm", () => {
        const wrapper = getWrapper();
        
        wrapper.vm.errorsField = 'ErrorField';
        wrapper.vm.onBeforeForHandlerUpdateFilm();
        
        expect(app.isRequest).toBe(true);
        expect(wrapper.vm.errorsField).toBe('');
    });
    
    it("Проверка функции onSuccessForHandlerUpdateFilm", () => {
        const wrapper = getWrapper();
        
        expect(hideModal).not.toHaveBeenCalled();
        wrapper.vm.onSuccessForHandlerUpdateFilm({props: {
                films: {
                    current_page: 17
                }
            }
        });
        
        expect(wrapper.vm.filmsAdmin.page).toBe(17);
        expect(hideModal).toHaveBeenCalledTimes(1);
        expect(hideModal).toHaveBeenCalledWith();
    });
    
    it("Проверка функции onErrorForHandlerUpdateFilm (валидация данных)", () => {
        setUpdateFilm('title', '');
        
        const wrapper = getWrapper();
        
        expect(wrapper.vm.errorsField).toBe('');
        // Берём в errors поле title, потому что изменятеся поле 'title'
        wrapper.vm.onErrorForHandlerUpdateFilm({title: 'ErrorTitle'});
        
        expect(wrapper.vm.errorsField).toBe('ErrorTitle');
    });
    
    it("Проверка функции onErrorForHandlerUpdateFilm (ошибка сервера с message)", () => {
        const wrapper = getWrapper();

        expect(hideModal).toHaveBeenCalledTimes(0);
        wrapper.vm.onErrorForHandlerUpdateFilm({ message: 'В таблице dvd.films нет записи с id=13' });
        expect(hideModal).toHaveBeenCalledTimes(1);
    });
    
    it("Проверка функции onFinishForHandlerUpdateFilm", async () => {
        app.isRequest = true;
        const wrapper = getWrapper();
        await flushPromises();
        
        wrapper.vm.onFinishForHandlerUpdateFilm();
        expect(app.isRequest).toBe(false);
    });
});
