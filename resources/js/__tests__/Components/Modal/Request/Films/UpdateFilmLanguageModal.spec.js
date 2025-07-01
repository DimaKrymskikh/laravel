import { flushPromises, mount } from "@vue/test-utils";
import { router } from '@inertiajs/vue3';

import { setActivePinia, createPinia } from 'pinia';
import { app } from '@/Services/app';
import { film } from '@/Services/Content/films';
import UpdateFilmLanguageModal from '@/Components/Modal/Request/Films/UpdateFilmLanguageModal.vue';
import { useFilmsAdminStore } from '@/Stores/films';

import { languages } from '@/__tests__/data/languages';
import { checkBaseModal } from '@/__tests__/methods/checkBaseModal';
import { checkInputField } from '@/__tests__/methods/checkInputField';
import { eventTargetClassListContainsFalseAndGetAttribute8 } from '@/__tests__/fake/Event';

vi.mock('@inertiajs/vue3');
        
const hideModal = vi.spyOn(film, 'hideUpdateFilmLanguageModal');

function setUpdateFilm() {
    film.id = 19;
    film.title = 'Бриллиантовая рука';
};

const getWrapper = function() {
    return mount(UpdateFilmLanguageModal, {
            global: {
                provide: {
                    filmsAdmin: useFilmsAdminStore()
                }
            }
        });
};

const checkContent = function(wrapper) {
    // Проверка равенства переменных ref начальным данным
    expect(wrapper.vm.filmLanguage).toBe('');
    expect(wrapper.vm.errorsName).toBe('');
    expect(wrapper.vm.languages).toStrictEqual(languages);

    // Заголовок модального окна задаётся
    expect(wrapper.text()).toContain(wrapper.vm.headerTitle);
};

describe("@/Components/Modal/Request/Films/UpdateFilmLanguageModal.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
        app.isRequest = false;
    });
    
    it("Монтирование компоненты UpdateFilmLanguageModal (isRequest: false)", async () => {
        app.request = vi.fn().mockImplementationOnce(() => languages);
        const wrapper = getWrapper();
        await flushPromises();
        
        checkContent(wrapper);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 1);
        checkInputField.checkPropsInputField(inputFields[0], 'Язык фильма:', 'text', wrapper.vm.errorsName, wrapper.vm.filmLanguage, true);
        checkInputField.checkInputFieldWhenThereIsNoRequest(inputFields[0], wrapper.vm.filmLanguage, 'Имя');
        
        const baseModal = checkBaseModal.getBaseModal(wrapper);
        checkBaseModal.checkPropsBaseModal(
                baseModal, wrapper.vm.headerTitle, wrapper.vm.hideModal
            );
        checkBaseModal.absenceOfHandlerSubmit(baseModal);
        await checkBaseModal.hideBaseModal(baseModal, hideModal);
    });
    
    it("Монтирование компоненты UpdateFilmActorsModal (isRequest: true)", async () => {
        app.request = vi.fn().mockImplementationOnce(() => languages);
        app.isRequest = true;
        const wrapper = getWrapper();
        await flushPromises();
        
        checkContent(wrapper);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 1);
        checkInputField.checkPropsInputField(inputFields[0], 'Язык фильма:', 'text', wrapper.vm.errorsName, wrapper.vm.filmLanguage, true);
        checkInputField.checkInputFieldWhenRequestIsMade(inputFields[0], wrapper.vm.filmLanguage, 'Имя');
        
        const baseModal = checkBaseModal.getBaseModal(wrapper);
        checkBaseModal.checkPropsBaseModal(
                baseModal, wrapper.vm.headerTitle, wrapper.vm.hideModal
            );
        checkBaseModal.absenceOfHandlerSubmit(baseModal);
        await checkBaseModal.notHideBaseModal(baseModal, hideModal);
    });
    
    it("Проверка изменения языка", async () => {
        app.request = vi.fn().mockImplementationOnce(() => languages);
        const wrapper = getWrapper();
        await flushPromises();
        
        const ul = wrapper.get('ul');
        
        const languagesLis = ul.findAll('li');
        expect(languagesLis.length).toBe(languages.length + 1);
        expect(router.put).not.toHaveBeenCalled();
        await languagesLis[1].trigger('click');
        expect(router.put).toHaveBeenCalledTimes(1);
    });
    
    it("Если список языков пуст, то отрисовывается нужная запись", async () => {
        app.request = vi.fn().mockImplementationOnce(() => []);
        const wrapper = getWrapper();
        await flushPromises();
        
        expect(wrapper.text()).toContain('Ничего не найдено');
    });
    
    it("Повторный запрос на сервер для изменения языка не отправляется", async () => {
        app.request = vi.fn().mockImplementationOnce(() => languages);
        // Создаём условие выполнения запроса
        app.isRequest = true;
        const wrapper = getWrapper();
        await flushPromises();
        
        const ul = wrapper.get('ul');
        const languagesLis = ul.findAll('li');
        expect(languagesLis.length).toBe(languages.length + 1);
        expect(router.put).not.toHaveBeenCalled();
        
        // Кликаем пару раз
        await languagesLis[1].trigger('click');
        await languagesLis[1].trigger('click');
        await flushPromises();
        // Запрос не отправился
        expect(router.put).not.toHaveBeenCalled();
    });
    
    it("Заполнение поля поиска input отправляет запрос на сервер (проверка watch)", async () => {
        vi.useFakeTimers();
        
        app.request = vi.fn().mockImplementationOnce(() => languages);
        const wrapper = getWrapper();
        await flushPromises();
        
        const appRequest = vi.spyOn(wrapper.vm.app, 'request');
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
    
    it("Функция handlerUpdateFilmLanguage вызывает router.put с нужными параметрами", async () => {
        const options = {
            preserveScroll: true,
            onBefore: expect.anything(),
            onSuccess: expect.anything(),
            onError: expect.anything(),
            onFinish: expect.anything()
        };
        
        setUpdateFilm();

        app.request = vi.fn().mockImplementationOnce(() => languages);
        const wrapper = getWrapper();
        await flushPromises();
        
        wrapper.vm.handlerUpdateFilmLanguage(eventTargetClassListContainsFalseAndGetAttribute8);
        
        expect(router.put).toHaveBeenCalledTimes(1);
        expect(router.put).toHaveBeenCalledWith(wrapper.vm.filmsAdmin.getUrl(`/admin/films/${film.id}`), {
                field: 'language_id',
                language_id: eventTargetClassListContainsFalseAndGetAttribute8.target.getAttribute('data-id')
            }, options);
    });
    
    it("Проверка функции onBeforeForHandlerUpdateFilmLanguage", async () => {
        app.request = vi.fn().mockImplementationOnce(() => languages);
        const wrapper = getWrapper();
        await flushPromises();
        
        wrapper.vm.errorsName = 'ErrorName';
        wrapper.vm.onBeforeForHandlerUpdateFilmLanguage();
        
        expect(wrapper.vm.app.isRequest).toBe(true);
        expect(wrapper.vm.errorsName).toBe('');
    });
    
    it("Проверка функции onSuccessForHandlerUpdateFilmLanguage", async () => {
        app.request = vi.fn().mockImplementationOnce(() => languages);
        const wrapper = getWrapper();
        await flushPromises();
        
        expect(hideModal).not.toHaveBeenCalled();
        wrapper.vm.onSuccessForHandlerUpdateFilmLanguage();
        
        expect(hideModal).toHaveBeenCalledTimes(1);
        expect(hideModal).toHaveBeenCalledWith();
    });
    
    it("Проверка функции onErrorForHandlerUpdateFilmLanguage", async () => {
        app.request = vi.fn().mockImplementationOnce(() => languages);
        const wrapper = getWrapper();
        await flushPromises();
        
        expect(wrapper.vm.errorsName).toBe('');
        wrapper.vm.onErrorForHandlerUpdateFilmLanguage({name: 'ErrorName'});
        
        expect(wrapper.vm.errorsName).toBe('ErrorName');
    });
    
    it("Проверка функции onFinishForHandlerUpdateFilmLanguage", async () => {
        app.request = vi.fn().mockImplementationOnce(() => languages);
        app.isRequest = true;
        const wrapper = getWrapper();
        await flushPromises();
        
        wrapper.vm.onFinishForHandlerUpdateFilmLanguage();
        expect(wrapper.vm.app.isRequest).toBe(false);
    });
});
