import { mount } from "@vue/test-utils";
import { router } from '@inertiajs/vue3';

import { setActivePinia, createPinia } from 'pinia';
import UpdateFilmModal from '@/Components/Modal/Request/Films/UpdateFilmModal.vue';
import { useAppStore } from '@/Stores/app';
import { useFilmsAdminStore } from '@/Stores/films';

import { checkBaseModal } from '@/__tests__/methods/checkBaseModal';
import { checkInputField } from '@/__tests__/methods/checkInputField';
import { eventCurrentTargetClassListContainsFalse } from '@/__tests__/fake/Event';

vi.mock('@inertiajs/vue3');
        
const hideUpdateFilmModal = vi.fn();
const updateFilm = {
    id: 8,
    title: 'Бриллиантовая рука',
    fieldValue: 'Значение изменяемого поля'
};

const getWrapper = function(app, filmsAdmin, field) {
    return mount(UpdateFilmModal, {
            props: {
                hideUpdateFilmModal,
                field,
                updateFilm
            },
            global: {
                provide: { app, filmsAdmin }
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
        const filmsAdmin = useFilmsAdminStore();

        const wrapper = getWrapper(app, filmsAdmin, 'title');
        
        checkContent(wrapper);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 1);
        checkInputField.checkPropsInputField(inputFields[0], wrapper.vm.titleText, 'text', wrapper.vm.errorsField, wrapper.vm.fieldValue, true);
        checkInputField.checkInputFieldWhenThereIsNoRequest(inputFields[0], wrapper.vm.fieldValue, 'Бриллиантовая нога');
        
        await checkBaseModal.hideBaseModal(wrapper, hideUpdateFilmModal);
        await checkBaseModal.submitRequestInBaseModal(wrapper, router.put);
    });
    
    it("Монтирование компоненты UpdateFilmModal (isRequest: false, field: description)", async () => {
        const app = useAppStore();
        const filmsAdmin = useFilmsAdminStore();

        const wrapper = getWrapper(app, filmsAdmin, 'description');
        
        checkContent(wrapper);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 1);
        checkInputField.checkPropsInputField(inputFields[0], wrapper.vm.titleText, 'text', wrapper.vm.errorsField, wrapper.vm.fieldValue, true);
        checkInputField.checkInputFieldWhenThereIsNoRequest(inputFields[0], wrapper.vm.fieldValue, 'Жулики хотят вернуть себе бриллианты любой ценой');
        
        await checkBaseModal.hideBaseModal(wrapper, hideUpdateFilmModal);
        await checkBaseModal.submitRequestInBaseModal(wrapper, router.put);
    });
    
    it("Монтирование компоненты UpdateFilmModal (isRequest: false, field: release_year)", async () => {
        const app = useAppStore();
        const filmsAdmin = useFilmsAdminStore();

        const wrapper = getWrapper(app, filmsAdmin, 'release_year');
        
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
        const filmsAdmin = useFilmsAdminStore();

        const wrapper = getWrapper(app, filmsAdmin, 'description');
        
        checkContent(wrapper);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 1);
        checkInputField.checkPropsInputField(inputFields[0], wrapper.vm.titleText, 'text', wrapper.vm.errorsField, wrapper.vm.fieldValue, true);
        checkInputField.checkInputFieldWhenRequestIsMade(inputFields[0], wrapper.vm.fieldValue, 'Жулики хотят вернуть себе бриллианты любой ценой');
        
        await checkBaseModal.notHideBaseModal(wrapper, hideUpdateFilmModal);
        await checkBaseModal.notSubmitRequestInBaseModal(wrapper, router.put);
    });
    
    it("Функция handlerUpdateFilm вызывает router.put с нужными параметрами", () => {
        const app = useAppStore();
        const filmsAdmin = useFilmsAdminStore();
        const options = {
            onBefore: expect.anything(),
            onSuccess: expect.anything(),
            onError: expect.anything(),
            onFinish: expect.anything()
        };

        const wrapper = getWrapper(app, filmsAdmin, 'title');
        
        wrapper.vm.handlerUpdateFilm(eventCurrentTargetClassListContainsFalse);
        
        expect(router.put).toHaveBeenCalledTimes(1);
        expect(router.put).toHaveBeenCalledWith(filmsAdmin.getUrl(`/admin/films/${wrapper.vm.props.updateFilm.id}`), {
                field: wrapper.vm.props.field,
                [wrapper.vm.props.field]: wrapper.vm.fieldValue
            }, options);
    });
    
    it("Проверка функции onBeforeForHandlerUpdateFilm", () => {
        const app = useAppStore();
        // По умолчанию
        expect(app.isRequest).toBe(false);
        const filmsAdmin = useFilmsAdminStore();
        
        const wrapper = getWrapper(app, filmsAdmin, 'title');
        wrapper.vm.errorsField = 'ErrorField';
        wrapper.vm.onBeforeForHandlerUpdateFilm();
        
        expect(app.isRequest).toBe(true);
        expect(wrapper.vm.errorsField).toBe('');
    });
    
    it("Проверка функции onSuccessForHandlerUpdateFilm", async () => {
        const app = useAppStore();
        const filmsAdmin = useFilmsAdminStore();
        // По умолчанию
        expect(filmsAdmin.page).toBe(1);
        
        const wrapper = getWrapper(app, filmsAdmin, 'title');
        
        expect(hideUpdateFilmModal).not.toHaveBeenCalled();
        wrapper.vm.onSuccessForHandlerUpdateFilm({props: {
                films: {
                    current_page: 17
                }
            }
        });
        
        expect(filmsAdmin.page).toBe(17);
        expect(hideUpdateFilmModal).toHaveBeenCalledTimes(1);
        expect(hideUpdateFilmModal).toHaveBeenCalledWith();
    });
    
    it("Проверка функции onErrorForHandlerUpdateFilm", async () => {
        const app = useAppStore();
        const filmsAdmin = useFilmsAdminStore();
        
        const wrapper = getWrapper(app, filmsAdmin, 'title');
        
        expect(wrapper.vm.errorsField).toBe('');
        // Берём в errors поле title, потому что изменятеся поле 'title'
        wrapper.vm.onErrorForHandlerUpdateFilm({title: 'ErrorTitle'});
        
        expect(wrapper.vm.errorsField).toBe('ErrorTitle');
    });
    
    it("Проверка функции onFinishForHandlerUpdateFilm", async () => {
        const app = useAppStore();
        app.isRequest = true;
        const filmsAdmin = useFilmsAdminStore();
        
        const wrapper = getWrapper(app, filmsAdmin, 'title');
        wrapper.vm.onFinishForHandlerUpdateFilm();
        
        expect(app.isRequest).toBe(false);
    });
});
