import { flushPromises, mount } from "@vue/test-utils";
import { router } from '@inertiajs/vue3';

import { setActivePinia, createPinia } from 'pinia';
import UpdateFilmLanguageModal from '@/Components/Modal/Request/Films/UpdateFilmLanguageModal.vue';
import { useAppStore } from '@/Stores/app';
import { useFilmsAdminStore } from '@/Stores/films';
import { updateFilm } from '@/Services/films';

import { languages } from '@/__tests__/data/languages';
import { checkBaseModal } from '@/__tests__/methods/checkBaseModal';
import { checkInputField } from '@/__tests__/methods/checkInputField';
import { eventTargetClassListContainsFalseAndGetAttribute8 } from '@/__tests__/fake/Event';

vi.mock('@inertiajs/vue3');
        
const hideUpdateFilmLanguageModal = vi.fn();

function setUpdateFilm() {
    updateFilm.id = 19;
    updateFilm.title = 'Бриллиантовая рука';
};

const getWrapper = function(app) {
    return mount(UpdateFilmLanguageModal, {
            props: {
                hideUpdateFilmLanguageModal,
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
        
        const wrapper = getWrapper(app);
        
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
        const wrapper = getWrapper(app);
        
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
        app.request = vi.fn()
            .mockImplementationOnce(() => languages);

        const wrapper = getWrapper(app);
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
        app.request = vi.fn()
            .mockImplementationOnce(() => []);

        const wrapper = getWrapper(app);
        await flushPromises();
        
        expect(wrapper.text()).toContain('Ничего не найдено');
    });
    
    it("Повторный запрос на сервер для изменения языка не отправляется", async () => {
        const app = useAppStore();
        // Компонента монтируется с условием, что запрос на сервер отправлен
        app.isRequest = true;
        app.request = vi.fn()
            .mockImplementationOnce(() => languages);

        const wrapper = getWrapper(app);
        await flushPromises();
        
        const ul = wrapper.get('ul');
        
        const languagesLis = ul.findAll('li');
        expect(languagesLis.length).toBe(languages.length + 1);
        expect(router.put).not.toHaveBeenCalled();
        await languagesLis[1].trigger('click');
        expect(router.put).not.toHaveBeenCalled();
    });
    
    it("Заполнение поля поиска input отправляет запрос на сервер (проверка watch)", async () => {
        vi.useFakeTimers();
        
        const app = useAppStore();
        const appRequest = vi.spyOn(app, 'request');

        const wrapper = getWrapper(app);
        await flushPromises();
        
        appRequest.mockClear();
        expect(appRequest).not.toHaveBeenCalled();
        // Нажимаем три клавиши, запрос отправляется один раз
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 1);
        checkInputField.checkInputFieldWhenThereIsNoRequest(inputFields[0], wrapper.vm.filmLanguage, 'a');
        // Чтобы тест отражал суть, нужно вызвать функцию flushPromises() после каждого ввода символа
        await flushPromises();
        checkInputField.checkInputFieldWhenThereIsNoRequest(inputFields[0], wrapper.vm.filmLanguage, 'b', 1);
        await flushPromises();
        checkInputField.checkInputFieldWhenThereIsNoRequest(inputFields[0], wrapper.vm.filmLanguage, 'c', 2);
        await flushPromises();
        
        vi.advanceTimersByTime(2000);
        expect(appRequest).toHaveBeenCalledTimes(1);
    });
    
    it("Функция handlerUpdateFilmLanguage вызывает router.put с нужными параметрами", () => {
        const app = useAppStore();
        const options = {
            preserveScroll: true,
            onBefore: expect.anything(),
            onSuccess: expect.anything(),
            onError: expect.anything(),
            onFinish: expect.anything()
        };
        
        setUpdateFilm();

        const wrapper = getWrapper(app);
        
        wrapper.vm.handlerUpdateFilmLanguage(eventTargetClassListContainsFalseAndGetAttribute8);
        
        expect(router.put).toHaveBeenCalledTimes(1);
        expect(router.put).toHaveBeenCalledWith(wrapper.vm.filmsAdmin.getUrl(`/admin/films/${updateFilm.id}`), {
                field: 'language_id',
                language_id: eventTargetClassListContainsFalseAndGetAttribute8.target.getAttribute('data-id')
            }, options);
    });
    
    it("Проверка функции onBeforeForHandlerUpdateFilmLanguage", () => {
        const app = useAppStore();
        // По умолчанию
        expect(app.isRequest).toBe(false);
        
        const wrapper = getWrapper(app);
        wrapper.vm.errorsName = 'ErrorName';
        wrapper.vm.onBeforeForHandlerUpdateFilmLanguage();
        
        expect(app.isRequest).toBe(true);
        expect(wrapper.vm.errorsName).toBe('');
    });
    
    it("Проверка функции onSuccessForHandlerUpdateFilmLanguage", async () => {
        const app = useAppStore();
        
        const wrapper = getWrapper(app);
        
        expect(hideUpdateFilmLanguageModal).not.toHaveBeenCalled();
        wrapper.vm.onSuccessForHandlerUpdateFilmLanguage();
        
        expect(hideUpdateFilmLanguageModal).toHaveBeenCalledTimes(1);
        expect(hideUpdateFilmLanguageModal).toHaveBeenCalledWith();
    });
    
    it("Проверка функции onErrorForHandlerUpdateFilmLanguage", async () => {
        const app = useAppStore();
        
        const wrapper = getWrapper(app);
        
        expect(wrapper.vm.errorsName).toBe('');
        wrapper.vm.onErrorForHandlerUpdateFilmLanguage({name: 'ErrorName'});
        
        expect(wrapper.vm.errorsName).toBe('ErrorName');
    });
    
    it("Проверка функции onFinishForHandlerUpdateFilmLanguage", async () => {
        const app = useAppStore();
        app.isRequest = true;
        
        const wrapper = getWrapper(app);
        wrapper.vm.onFinishForHandlerUpdateFilmLanguage();
        
        expect(app.isRequest).toBe(false);
    });
});
