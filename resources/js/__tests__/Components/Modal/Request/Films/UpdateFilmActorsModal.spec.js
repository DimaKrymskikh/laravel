import { flushPromises, mount } from "@vue/test-utils";
import { router } from '@inertiajs/vue3';

import { setActivePinia, createPinia } from 'pinia';
import UpdateFilmActorsModal from '@/Components/Modal/Request/Films/UpdateFilmActorsModal.vue';
import Spinner from '@/components/Svg/Spinner.vue';
import { useAppStore } from '@/Stores/app';
import { useFilmsAdminStore } from '@/Stores/films';

import { json_film_actors, json_film_actors_0, json_free_actors, json_free_actors_0 } from '@/__tests__/data/actors';
import { checkBaseModal } from '@/__tests__/methods/checkBaseModal';
import { checkInputField } from '@/__tests__/methods/checkInputField';
import { eventTargetClassListContainsFalseAndGetAttribute8 } from '@/__tests__/fake/Event';

vi.mock('@inertiajs/vue3');
        
const hideUpdateFilmActorsModal = vi.fn();
const showRemoveActorFromFilmModal = vi.fn();

const updateFilm = {
    id: 19,
    title: 'Бриллиантовая рука',
    fieldValue: ''
};

const getWrapper = function(app) {
    return mount(UpdateFilmActorsModal, {
            props: {
                hideUpdateFilmActorsModal,
                showRemoveActorFromFilmModal,
                updateFilm
            },
            global: {
                provide: {
                    app,
                    filmsAdmin: useFilmsAdminStore()
                }
            }
        });
};
//
const checkContent = function(wrapper) {
    // Проверка равенства переменных ref начальным данным
    expect(wrapper.vm.actorName).toBe('');
    expect(wrapper.vm.filmActors).toBe(null);
    expect(wrapper.vm.actors).toBe(null);

    // Заголовок модального окна задаётся
    expect(wrapper.text()).toContain(wrapper.vm.headerTitle);
};

describe("@/Components/Modal/Request/Films/UpdateFilmActorsModal.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    
    it("Монтирование компоненты UpdateFilmActorsModal (isRequest: false)", async () => {
        const app = useAppStore();

        const wrapper = getWrapper(app);
        
        checkContent(wrapper);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 1);
        checkInputField.checkPropsInputField(inputFields[0], 'Фильтр поиска актёров фильма:', 'text', undefined, wrapper.vm.actorName, true);
        checkInputField.checkInputFieldWhenThereIsNoRequest(inputFields[0], wrapper.vm.actorName, 'Имя');
        
        const baseModal = checkBaseModal.getBaseModal(wrapper);
        checkBaseModal.checkPropsBaseModal(
                baseModal, wrapper.vm.headerTitle, hideUpdateFilmActorsModal
            );
        checkBaseModal.absenceOfHandlerSubmit(baseModal);
        await checkBaseModal.hideBaseModal(baseModal, hideUpdateFilmActorsModal);
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
        checkInputField.checkPropsInputField(inputFields[0], 'Фильтр поиска актёров фильма:', 'text', undefined, wrapper.vm.actorName, true);
        checkInputField.checkInputFieldWhenRequestIsMade(inputFields[0], wrapper.vm.actorName, 'Имя');
        
        const baseModal = checkBaseModal.getBaseModal(wrapper);
        checkBaseModal.checkPropsBaseModal(
                baseModal, wrapper.vm.headerTitle, hideUpdateFilmActorsModal
            );
        checkBaseModal.absenceOfHandlerSubmit(baseModal);
        await checkBaseModal.notHideBaseModal(baseModal, hideUpdateFilmActorsModal);
    });
    
    it("Проверка событий при клике по актёрам", async () => {
        const app = useAppStore();

        app.request = vi.fn()
            .mockImplementationOnce(() => json_free_actors)
            .mockImplementationOnce(() => json_film_actors);

        const wrapper = getWrapper(app);
        await flushPromises();
        
        const uls = wrapper.findAll('ul');
        expect(uls.length).toBe(2);
        
        const filmActorsUl = uls[0];
        const filmActorsLis = filmActorsUl.findAll('li');
        expect(filmActorsLis.length).toBe(json_film_actors.actors.length);
        // Открывается модальное окно для удаления актёра из фильма
        expect(showRemoveActorFromFilmModal).not.toHaveBeenCalled();
        await filmActorsLis[1].trigger('click');
        expect(showRemoveActorFromFilmModal).toHaveBeenCalledTimes(1);
        
        const actorsUl = uls[1];
        const actorsLis = actorsUl.findAll('li');
        expect(actorsLis.length).toBe(json_free_actors.length);
        // Отправляется запрос на сервер, добавляющий актёра в фильм
        expect(router.post).not.toHaveBeenCalled();
        await actorsLis[3].trigger('click');
        expect(router.post).toHaveBeenCalledTimes(1);
    });
    
    it("Если список актёров пуст, появляются нужные записи", async () => {
        const app = useAppStore();

        app.request = vi.fn()
            .mockImplementationOnce(() => json_free_actors_0)
            .mockImplementationOnce(() => json_film_actors_0);

        const wrapper = getWrapper(app);
        await flushPromises();
        
        expect(wrapper.text()).toContain('Актёры не добавлены');
        expect(wrapper.text()).toContain('Ничего не найдено');
    });
    
    it("Если список актёров null, появляются нужные записи", async () => {
        const app = useAppStore();

        app.request = vi.fn()
            .mockImplementationOnce(() => null)
            .mockImplementationOnce(() => null);

        const wrapper = getWrapper(app);
        await flushPromises();
        
        expect(wrapper.text()).toContain('Актёры не добавлены');
        expect(wrapper.text()).toContain('Ничего не найдено');
    });
    
    it("Во время запроса отсутствует список актёров", async () => {
        const app = useAppStore();
        // Компонента монтируется с условием, что запрос на сервер отправлен
        app.isRequest = true;

        app.request = vi.fn()
            .mockImplementationOnce(() => json_free_actors)
            .mockImplementationOnce(() => json_film_actors);

        const wrapper = getWrapper(app);
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
        
        const app = useAppStore();
        const appRequest = vi.spyOn(app, 'request');

        const wrapper = getWrapper(app);
        // При сборке компоненты app.request вызывается дважды: общий список актёров и список актёров фильма
        await flushPromises();
        expect(appRequest).toHaveBeenCalledTimes(2);
        // Очищаем вызовы
        appRequest.mockClear();
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
    
    it("Функция handlerAddActorInFilm вызывает router.post с нужными параметрами", () => {
        const app = useAppStore();
        const options = {
            preserveScroll: true,
            onBefore: expect.anything(),
            onSuccess: expect.anything(),
            onFinish: expect.anything()
        };

        const wrapper = getWrapper(app);
        
        wrapper.vm.handlerAddActorInFilm(eventTargetClassListContainsFalseAndGetAttribute8);
        
        expect(router.post).toHaveBeenCalledTimes(1);
        expect(router.post).toHaveBeenCalledWith(wrapper.vm.filmsAdmin.getUrl('/admin/films/actors'), {
                film_id: wrapper.vm.props.updateFilm.id,
                actor_id: eventTargetClassListContainsFalseAndGetAttribute8.target.getAttribute('data-id')
            }, options);
    });
    
    it("Проверка функции onBeforeForHandlerAddActorInFilm", () => {
        const app = useAppStore();
        // По умолчанию
        expect(app.isRequest).toBe(false);
        
        const wrapper = getWrapper(app);
        wrapper.vm.onBeforeForHandlerAddActorInFilm();
        
        expect(app.isRequest).toBe(true);
    });
    
    it("Проверка функции onSuccessForHandlerAddActorInFilm", async () => {
        const app = useAppStore();
        
        const wrapper = getWrapper(app);
        
        expect(hideUpdateFilmActorsModal).not.toHaveBeenCalled();
        wrapper.vm.onSuccessForHandlerAddActorInFilm();
        
        expect(hideUpdateFilmActorsModal).toHaveBeenCalledTimes(1);
        expect(hideUpdateFilmActorsModal).toHaveBeenCalledWith();
    });
    
    it("Проверка функции onFinishForHandlerAddActorInFilm", async () => {
        const app = useAppStore();
        app.isRequest = true;
        
        const wrapper = getWrapper(app);
        wrapper.vm.onFinishForHandlerAddActorInFilm();
        
        expect(app.isRequest).toBe(false);
    });
});
