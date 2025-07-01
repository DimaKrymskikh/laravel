import { flushPromises, mount } from "@vue/test-utils";
import { router } from '@inertiajs/vue3';

import { setActivePinia, createPinia } from 'pinia';
import { app } from '@/Services/app';
import { film, removeActor } from '@/Services/Content/films';
import UpdateFilmActorsModal from '@/Components/Modal/Request/Films/UpdateFilmActorsModal.vue';
import Spinner from '@/components/Svg/Spinner.vue';
import { useFilmsAdminStore } from '@/Stores/films';

import { json_film_actors, json_film_actors_0, json_free_actors, json_free_actors_0 } from '@/__tests__/data/actors';
import { checkBaseModal } from '@/__tests__/methods/checkBaseModal';
import { checkInputField } from '@/__tests__/methods/checkInputField';
import { eventTargetClassListContainsFalseAndGetAttribute8 } from '@/__tests__/fake/Event';

vi.mock('@inertiajs/vue3');
        
const hideModal = vi.spyOn(film, 'hideUpdateFilmActorsModal');
const showModal = vi.spyOn(removeActor, 'showRemoveActorFromFilmModal');

const getWrapper = function() {
    return mount(UpdateFilmActorsModal, {
            global: {
                provide: {
                    filmsAdmin: useFilmsAdminStore()
                }
            }
        });
};
//
const checkContent = function(wrapper) {
    // Проверка равенства переменных ref начальным данным
    expect(wrapper.vm.actorName).toBe('');
    expect(wrapper.vm.filmActors).toStrictEqual(json_film_actors);
    expect(wrapper.vm.actors).toStrictEqual(json_free_actors);

    // Заголовок модального окна задаётся
    expect(wrapper.text()).toContain(wrapper.vm.headerTitle);
};

describe("@/Components/Modal/Request/Films/UpdateFilmActorsModal.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
        app.isRequest = false;
    });
    
    it("Монтирование компоненты UpdateFilmActorsModal (isRequest: false)", async () => {
        app.request = vi.fn()
            .mockImplementationOnce(() => json_free_actors)
            .mockImplementationOnce(() => json_film_actors);
    
        const wrapper = getWrapper();
        await flushPromises();
        
        checkContent(wrapper);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 1);
        checkInputField.checkPropsInputField(inputFields[0], 'Фильтр поиска актёров фильма:', 'text', undefined, wrapper.vm.actorName, true);
        checkInputField.checkInputFieldWhenThereIsNoRequest(inputFields[0], wrapper.vm.actorName, 'Имя');
        
        const baseModal = checkBaseModal.getBaseModal(wrapper);
        checkBaseModal.checkPropsBaseModal(
                baseModal, wrapper.vm.headerTitle, wrapper.vm.hideModal
            );
        checkBaseModal.absenceOfHandlerSubmit(baseModal);
        await checkBaseModal.hideBaseModal(baseModal, film.hideUpdateFilmActorsModal);
    });
    
    it("Монтирование компоненты UpdateFilmActorsModal (isRequest: true)", async () => {
        app.isRequest = true;
        app.request = vi.fn()
            .mockImplementationOnce(() => json_free_actors)
            .mockImplementationOnce(() => json_film_actors);
    
        const wrapper = getWrapper();
        await flushPromises();
        
        checkContent(wrapper);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 1);
        checkInputField.checkPropsInputField(inputFields[0], 'Фильтр поиска актёров фильма:', 'text', undefined, wrapper.vm.actorName, true);
        checkInputField.checkInputFieldWhenRequestIsMade(inputFields[0], wrapper.vm.actorName, 'Имя');
        
        const baseModal = checkBaseModal.getBaseModal(wrapper);
        checkBaseModal.checkPropsBaseModal(
                baseModal, wrapper.vm.headerTitle, wrapper.vm.hideModal
            );
        checkBaseModal.absenceOfHandlerSubmit(baseModal);
        await checkBaseModal.notHideBaseModal(baseModal, film.hideUpdateFilmActorsModal);
    });
    
    it("Проверка событий при клике по актёрам", async () => {
        app.request = vi.fn()
            .mockImplementationOnce(() => json_free_actors)
            .mockImplementationOnce(() => json_film_actors);
    
        const wrapper = getWrapper();
        await flushPromises();
        
        const uls = wrapper.findAll('ul');
        expect(uls.length).toBe(2);
        
        const filmActorsUl = uls[0];
        const filmActorsLis = filmActorsUl.findAll('li');
        expect(filmActorsLis.length).toBe(json_film_actors.actors.length);
        // Открывается модальное окно для удаления актёра из фильма
        expect(showModal).not.toHaveBeenCalled();
        await filmActorsLis[1].trigger('click');
        expect(showModal).toHaveBeenCalledTimes(1);
        
        const actorsUl = uls[1];
        const actorsLis = actorsUl.findAll('li');
        expect(actorsLis.length).toBe(json_free_actors.length);
        // Отправляется запрос на сервер, добавляющий актёра в фильм
        expect(router.post).not.toHaveBeenCalled();
        await actorsLis[3].trigger('click');
        expect(router.post).toHaveBeenCalledTimes(1);
    });
    
    it("Если список актёров пуст, появляются нужные записи", async () => {
        app.request = vi.fn()
            .mockImplementationOnce(() => json_free_actors_0)
            .mockImplementationOnce(() => json_film_actors_0);
    
        const wrapper = getWrapper();
        await flushPromises();
        
        expect(wrapper.text()).toContain('Актёры не добавлены');
        expect(wrapper.text()).toContain('Ничего не найдено');
    });
    
    it("Если список актёров null, появляются нужные записи", async () => {
        app.request = vi.fn()
            .mockImplementationOnce(() => null)
            .mockImplementationOnce(() => null);
    
        const wrapper = getWrapper(false, null, null);
        await flushPromises();
        
        expect(wrapper.text()).toContain('Актёры не добавлены');
        expect(wrapper.text()).toContain('Ничего не найдено');
    });
    
    it("Во время запроса отсутствует список актёров", async () => {
        // Компонента монтируется с условием, что запрос на сервер отправлен
        app.isRequest = true;
        app.request = vi.fn()
            .mockImplementationOnce(() => json_free_actors)
            .mockImplementationOnce(() => json_film_actors);
    
        const wrapper = getWrapper();
        await flushPromises();
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 1);
        
        // Отсутствуют списки актёров
        const uls = wrapper.findAll('ul');
        expect(uls.length).toBe(0);
        // Спиннеры отображаются
        const spinner = wrapper.findAllComponents(Spinner)
        expect(spinner.length).toBe(3);
    });
    
    it("Заполнение поля поиска актёров input отправляет запрос на сервер (проверка watch)", async () => {
        vi.useFakeTimers();
        
        app.request = vi.fn()
            .mockImplementationOnce(() => json_free_actors)
            .mockImplementationOnce(() => json_film_actors);
        const appRequest = vi.spyOn(app, 'request');
    
        const wrapper = getWrapper();
        await flushPromises();
        // При монтировании компоненты app.request вызывается дважды.
        // В данном тесте это не проверяем.
        app.request.mockClear();
        
        expect(appRequest).not.toHaveBeenCalled();
        // Нажимаем три клавиши, запрос отправляется один раз
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 1);
        checkInputField.checkInputFieldWhenThereIsNoRequest(inputFields[0], wrapper.vm.actorName, 'a');
        // Чтобы тест отражал суть, нужно вызвать функцию flushPromises() после каждого ввода символа
        await flushPromises();
        checkInputField.checkInputFieldWhenThereIsNoRequest(inputFields[0], wrapper.vm.actorName, 'b', 1);
        await flushPromises();
        checkInputField.checkInputFieldWhenThereIsNoRequest(inputFields[0], wrapper.vm.actorName, 'c', 2);
        await flushPromises();

        vi.advanceTimersByTime(2000);
        expect(appRequest).toHaveBeenCalledTimes(1);
    });
    
    it("Функция handlerAddActorInFilm вызывает router.post с нужными параметрами", async () => {
        const options = {
            preserveScroll: true,
            onBefore: expect.anything(),
            onSuccess: expect.anything(),
            onFinish: expect.anything()
        };

        app.request = vi.fn()
            .mockImplementationOnce(() => json_free_actors)
            .mockImplementationOnce(() => json_film_actors);
    
        const wrapper = getWrapper();
        await flushPromises();
        
        wrapper.vm.handlerAddActorInFilm(eventTargetClassListContainsFalseAndGetAttribute8);
        
        expect(router.post).toHaveBeenCalledTimes(1);
        expect(router.post).toHaveBeenCalledWith(wrapper.vm.filmsAdmin.getUrl('/admin/films/actors'), {
                film_id: film.id,
                actor_id: eventTargetClassListContainsFalseAndGetAttribute8.target.getAttribute('data-id')
            }, options);
    });
    
    it("Проверка функции onBeforeForHandlerAddActorInFilm", async () => {
        app.request = vi.fn()
            .mockImplementationOnce(() => json_free_actors)
            .mockImplementationOnce(() => json_film_actors);
    
        const wrapper = getWrapper();
        await flushPromises();
        
        wrapper.vm.onBeforeForHandlerAddActorInFilm();
        expect(wrapper.vm.app.isRequest).toBe(true);
    });
    
    it("Проверка функции onSuccessForHandlerAddActorInFilm", async () => {
        app.request = vi.fn()
            .mockImplementationOnce(() => json_free_actors)
            .mockImplementationOnce(() => json_film_actors);
    
        const wrapper = getWrapper();
        await flushPromises();
        
        expect(hideModal).not.toHaveBeenCalled();
        wrapper.vm.onSuccessForHandlerAddActorInFilm();
        
        expect(hideModal).toHaveBeenCalledTimes(1);
        expect(hideModal).toHaveBeenCalledWith();
    });
    
    it("Проверка функции onFinishForHandlerAddActorInFilm", async () => {
        app.isRequest = true;
        app.request = vi.fn()
            .mockImplementationOnce(() => json_free_actors)
            .mockImplementationOnce(() => json_film_actors);
    
        const wrapper = getWrapper();
        await flushPromises();
        
        wrapper.vm.onFinishForHandlerAddActorInFilm();
        expect(app.isRequest).toBe(false);
    });
});
