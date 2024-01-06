import { flushPromises, mount } from "@vue/test-utils";
import { router } from '@inertiajs/vue3';

import { setActivePinia, createPinia } from 'pinia';
import UpdateFilmLanguageModal from '@/Components/Modal/Request/Films/UpdateFilmLanguageModal.vue';
import { useAppStore } from '@/Stores/app';
import { useFilmsAdminStore } from '@/Stores/films';

import { languages } from '@/__tests__/data/languages';
import { checkBaseModal } from '@/__tests__/methods/checkBaseModal';
import { checkInputField } from '@/__tests__/methods/checkInputField';

vi.mock('@inertiajs/vue3');
        
const hideUpdateFilmLanguageModal = vi.fn();
const updateFilm = {
    id: 19,
    title: 'Бриллиантовая рука',
    fieldValue: ''
};

const getWrapper = function(app, filmsAdmin) {
    return mount(UpdateFilmLanguageModal, {
            props: {
                hideUpdateFilmLanguageModal,
                updateFilm
            },
            global: {
                provide: { app, filmsAdmin }
            }
        });
};

const checkContent = function(wrapper) {
    // Проверка равенства переменных ref начальным данным
    expect(wrapper.vm.filmLanguage).toBe('');
    expect(wrapper.vm.errorsName).toBe('');
    expect(wrapper.vm.languages).toBe(null);

    // Заголовок модального окна задаётся
    expect(wrapper.text()).toContain(wrapper.vm.headerTitle);
};

describe("@/Components/Modal/Request/Films/UpdateFilmLanguageModal.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    
    it("Монтирование компоненты UpdateFilmLanguageModal (isRequest: false)", async () => {
        const app = useAppStore();
        const filmsAdmin = useFilmsAdminStore();

        const wrapper = getWrapper(app, filmsAdmin);
        
        checkContent(wrapper);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 1);
        checkInputField.checkPropsInputField(inputFields[0], 'Язык фильма:', 'text', wrapper.vm.errorsName, wrapper.vm.filmLanguage, true);
        checkInputField.checkInputFieldWhenThereIsNoRequest(inputFields[0], wrapper.vm.filmLanguage, 'Имя');
        
        const baseModal = checkBaseModal.getBaseModal(wrapper);
        checkBaseModal.checkPropsBaseModal(
                baseModal, wrapper.vm.headerTitle, hideUpdateFilmLanguageModal
            );
        checkBaseModal.absenceOfHandlerSubmit(baseModal);
        await checkBaseModal.hideBaseModal(baseModal, hideUpdateFilmLanguageModal);
    });
    
    it("Монтирование компоненты UpdateFilmActorsModal (isRequest: true)", async () => {
        const app = useAppStore();
        app.isRequest = true;
        // Метод app.request выполняется при монтировании компоненты и устанавливает app.isRequest = false,
        // поэтому применяем мок-функцию
        app.request = vi.fn();
        const filmsAdmin = useFilmsAdminStore();

        const wrapper = getWrapper(app, filmsAdmin);
        
        checkContent(wrapper);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 1);
        checkInputField.checkPropsInputField(inputFields[0], 'Язык фильма:', 'text', wrapper.vm.errorsName, wrapper.vm.filmLanguage, true);
        checkInputField.checkInputFieldWhenRequestIsMade(inputFields[0], wrapper.vm.filmLanguage, 'Имя');
        
        const baseModal = checkBaseModal.getBaseModal(wrapper);
        checkBaseModal.checkPropsBaseModal(
                baseModal, wrapper.vm.headerTitle, hideUpdateFilmLanguageModal
            );
        checkBaseModal.absenceOfHandlerSubmit(baseModal);
        await checkBaseModal.notHideBaseModal(baseModal, hideUpdateFilmLanguageModal);
    });
    
    it("Проверка изменения языка", async () => {
        const app = useAppStore();
        const filmsAdmin = useFilmsAdminStore();

        app.request = vi.fn()
            .mockImplementationOnce(() => languages);

        const wrapper = getWrapper(app, filmsAdmin);
        await flushPromises();
        
        const ul = wrapper.get('ul');
        
        const languagesLis = ul.findAll('li');
        expect(languagesLis.length).toBe(languages.length + 1);
        expect(router.put).not.toHaveBeenCalled();
        await languagesLis[1].trigger('click');
        expect(router.put).toHaveBeenCalledTimes(1);
    });
    
    it("Если список языков пуст, то отрисовывается нужная запись", async () => {
        const app = useAppStore();
        const filmsAdmin = useFilmsAdminStore();

        app.request = vi.fn()
            .mockImplementationOnce(() => []);

        const wrapper = getWrapper(app, filmsAdmin);
        await flushPromises();
        
        expect(wrapper.text()).toContain('Ничего не найдено');
    });
    
    it("Повторный запрос на сервер для изменения языка не отправляется", async () => {
        const app = useAppStore();
        // Компонента монтируется с условием, что запрос на сервер отправлен
        app.isRequest = true;
        const filmsAdmin = useFilmsAdminStore();

        app.request = vi.fn()
            .mockImplementationOnce(() => languages);

        const wrapper = getWrapper(app, filmsAdmin);
        await flushPromises();
        
        const ul = wrapper.get('ul');
        
        const languagesLis = ul.findAll('li');
        expect(languagesLis.length).toBe(languages.length + 1);
        expect(router.put).not.toHaveBeenCalled();
        await languagesLis[1].trigger('click');
        expect(router.put).not.toHaveBeenCalled();
    });
    
    it("Заполнение поля поиска input отправляет запрос на сервер (проверка watch)", async () => {
        const app = useAppStore();
        const appRequest = vi.spyOn(app, 'request');
        const filmsAdmin = useFilmsAdminStore();

        const wrapper = getWrapper(app, filmsAdmin);
        await flushPromises();
        
        appRequest.mockClear();
        expect(appRequest).not.toHaveBeenCalled();
        // После ввода одного символа отправляется запрос на сервер
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 1);
        checkInputField.checkInputFieldWhenThereIsNoRequest(inputFields[0], wrapper.vm.filmLanguage, 'Р');
        await flushPromises();
        expect(appRequest).toHaveBeenCalledTimes(1);
    });
});
