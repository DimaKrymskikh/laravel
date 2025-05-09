import { mount } from "@vue/test-utils";
import { router } from '@inertiajs/vue3';

import { setActivePinia, createPinia } from 'pinia';
import UpdateFilmModal from '@/Components/Modal/Request/Films/UpdateFilmModal.vue';
import { useAppStore } from '@/Stores/app';
import { useFilmsAdminStore } from '@/Stores/films';
import { updateFilm } from '@/Services/films';

import { checkBaseModal } from '@/__tests__/methods/checkBaseModal';
import { checkInputField } from '@/__tests__/methods/checkInputField';
import { eventCurrentTargetClassListContainsFalse } from '@/__tests__/fake/Event';

vi.mock('@inertiajs/vue3');
        
const hideUpdateFilmModal = vi.fn();

function setUpdateFilm(field, fieldValue) {
    updateFilm.id = 8;
    updateFilm.title = 'Бриллиантовая рука';
    updateFilm.field = field;
    updateFilm.fieldValue = fieldValue;
};

const getWrapper = function(app) {
    return mount(UpdateFilmModal, {
            props: {
                hideUpdateFilmModal
            },
            global: {
                provide: {
                    app,
                    filmsAdmin: useFilmsAdminStore()
                }
            }
        });
};

const checkContent = function(wrapper) {
    // Проверка равенства переменных ref начальным данным
    expect(wrapper.vm.fieldValue).toBe(updateFilm.fieldValue);
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
        const app = useAppStore();
        
        setUpdateFilm('title', '');

        const wrapper = getWrapper(app);
        
        checkContent(wrapper);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 1);
        checkInputField.checkPropsInputField(inputFields[0], wrapper.vm.titleText, 'text', wrapper.vm.errorsField, wrapper.vm.fieldValue, true);
        checkInputField.checkInputFieldWhenThereIsNoRequest(inputFields[0], wrapper.vm.fieldValue, 'Бриллиантовая нога');
        
        await checkBaseModal.hideBaseModal(wrapper, hideUpdateFilmModal);
        await checkBaseModal.submitRequestInBaseModal(wrapper, router.put);
    });
    
    it("Монтирование компоненты UpdateFilmModal (isRequest: false, field: description)", async () => {
        const app = useAppStore();
        
        setUpdateFilm('description', '');

        const wrapper = getWrapper(app);
        
        checkContent(wrapper);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 1);
        checkInputField.checkPropsInputField(inputFields[0], wrapper.vm.titleText, 'text', wrapper.vm.errorsField, wrapper.vm.fieldValue, true);
        checkInputField.checkInputFieldWhenThereIsNoRequest(inputFields[0], wrapper.vm.fieldValue, 'Жулики хотят вернуть себе бриллианты любой ценой');
        
        await checkBaseModal.hideBaseModal(wrapper, hideUpdateFilmModal);
        await checkBaseModal.submitRequestInBaseModal(wrapper, router.put);
    });
    
    it("Монтирование компоненты UpdateFilmModal (isRequest: false, field: release_year)", async () => {
        const app = useAppStore();
        
        setUpdateFilm('release_year', '');

        const wrapper = getWrapper(app);
        
        checkContent(wrapper);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 1);
        checkInputField.checkPropsInputField(inputFields[0], wrapper.vm.titleText, 'text', wrapper.vm.errorsField, wrapper.vm.fieldValue, true);
        checkInputField.checkInputFieldWhenThereIsNoRequest(inputFields[0], wrapper.vm.fieldValue, '1970');
        
        await checkBaseModal.hideBaseModal(wrapper, hideUpdateFilmModal);
        await checkBaseModal.submitRequestInBaseModal(wrapper, router.put);
    });
    
    it("Монтирование компоненты UpdateFilmModal (isRequest: true)", async () => {
        const app = useAppStore();
        app.isRequest = true;
        
        setUpdateFilm('description', '');

        const wrapper = getWrapper(app);
        
        checkContent(wrapper);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 1);
        checkInputField.checkPropsInputField(inputFields[0], wrapper.vm.titleText, 'text', wrapper.vm.errorsField, wrapper.vm.fieldValue, true);
        checkInputField.checkInputFieldWhenRequestIsMade(inputFields[0], wrapper.vm.fieldValue, 'Жулики хотят вернуть себе бриллианты любой ценой');
        
        await checkBaseModal.notHideBaseModal(wrapper, hideUpdateFilmModal);
        await checkBaseModal.notSubmitRequestInBaseModal(wrapper, router.put);
    });
    
    it("Функция handlerUpdateFilm вызывает router.put с нужными параметрами", () => {
        const app = useAppStore();
        const options = {
            onBefore: expect.anything(),
            onSuccess: expect.anything(),
            onError: expect.anything(),
            onFinish: expect.anything()
        };
        
        setUpdateFilm('description', '');

        const wrapper = getWrapper(app);
        
        wrapper.vm.handlerUpdateFilm(eventCurrentTargetClassListContainsFalse);
        
        expect(router.put).toHaveBeenCalledTimes(1);
        expect(router.put).toHaveBeenCalledWith(wrapper.vm.filmsAdmin.getUrl(`/admin/films/${updateFilm.id}`), {
                field: updateFilm.field,
                [updateFilm.field]: wrapper.vm.fieldValue
            }, options);
    });
    
    it("Проверка функции onBeforeForHandlerUpdateFilm", () => {
        const app = useAppStore();
        // По умолчанию
        expect(app.isRequest).toBe(false);
        
        const wrapper = getWrapper(app);
        wrapper.vm.errorsField = 'ErrorField';
        wrapper.vm.onBeforeForHandlerUpdateFilm();
        
        expect(app.isRequest).toBe(true);
        expect(wrapper.vm.errorsField).toBe('');
    });
    
    it("Проверка функции onSuccessForHandlerUpdateFilm", () => {
        const app = useAppStore();
        
        const wrapper = getWrapper(app);
        
        expect(hideUpdateFilmModal).not.toHaveBeenCalled();
        wrapper.vm.onSuccessForHandlerUpdateFilm({props: {
                films: {
                    current_page: 17
                }
            }
        });
        
        expect(wrapper.vm.filmsAdmin.page).toBe(17);
        expect(hideUpdateFilmModal).toHaveBeenCalledTimes(1);
        expect(hideUpdateFilmModal).toHaveBeenCalledWith();
    });
    
    it("Проверка функции onErrorForHandlerUpdateFilm (валидация данных)", () => {
        const app = useAppStore();
        
        setUpdateFilm('title', '');
        
        const wrapper = getWrapper(app);
        
        expect(wrapper.vm.errorsField).toBe('');
        // Берём в errors поле title, потому что изменятеся поле 'title'
        wrapper.vm.onErrorForHandlerUpdateFilm({title: 'ErrorTitle'});
        
        expect(wrapper.vm.errorsField).toBe('ErrorTitle');
    });
    
    it("Проверка функции onErrorForHandlerUpdateFilm (ошибка сервера с message)", () => {
        const app = useAppStore();
        
        const wrapper = getWrapper(app);

        expect(hideUpdateFilmModal).toHaveBeenCalledTimes(0);
        wrapper.vm.onErrorForHandlerUpdateFilm({ message: 'В таблице dvd.films нет записи с id=13' });
        expect(hideUpdateFilmModal).toHaveBeenCalledTimes(1);
    });
    
    it("Проверка функции onFinishForHandlerUpdateFilm", () => {
        const app = useAppStore();
        app.isRequest = true;
        
        const wrapper = getWrapper(app);
        wrapper.vm.onFinishForHandlerUpdateFilm();
        
        expect(app.isRequest).toBe(false);
    });
});
