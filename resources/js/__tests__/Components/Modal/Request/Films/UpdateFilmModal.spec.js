import { mount } from "@vue/test-utils";
import { router } from '@inertiajs/vue3';

import { setActivePinia, createPinia } from 'pinia';
import UpdateFilmModal from '@/Components/Modal/Request/Films/UpdateFilmModal.vue';
import { useAppStore } from '@/Stores/app';
import { useFilmsAdminStore } from '@/Stores/films';

import { checkBaseModal } from '@/__tests__/methods/checkBaseModal';
import { checkInputField } from '@/__tests__/methods/checkInputField';

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
});
