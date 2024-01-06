import { flushPromises, mount } from "@vue/test-utils";
import { router } from '@inertiajs/vue3';

import { setActivePinia, createPinia } from 'pinia';
import UpdateFilmActorsModal from '@/Components/Modal/Request/Films/UpdateFilmActorsModal.vue';
import { useAppStore } from '@/Stores/app';
import { useFilmsAdminStore } from '@/Stores/films';

import { json_film_actors, json_film_actors_0, json_free_actors, json_free_actors_0 } from '@/__tests__/data/actors';
import { checkBaseModal } from '@/__tests__/methods/checkBaseModal';
import { checkInputField } from '@/__tests__/methods/checkInputField';

vi.mock('@inertiajs/vue3');
        
const hideUpdateFilmActorsModal = vi.fn();
const showRemoveActorFromFilmModal = vi.fn();

const updateFilm = {
    id: 19,
    title: 'Бриллиантовая рука',
    fieldValue: ''
};

const getWrapper = function(app, filmsAdmin) {
    return mount(UpdateFilmActorsModal, {
            props: {
                hideUpdateFilmActorsModal,
                showRemoveActorFromFilmModal,
                updateFilm
            },
            global: {
                provide: { app, filmsAdmin }
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
        const filmsAdmin = useFilmsAdminStore();

        const wrapper = getWrapper(app, filmsAdmin);
        
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
        const filmsAdmin = useFilmsAdminStore();

        const wrapper = getWrapper(app, filmsAdmin);
        
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
        const filmsAdmin = useFilmsAdminStore();

        app.request = vi.fn()
            .mockImplementationOnce(() => json_free_actors)
            .mockImplementationOnce(() => json_film_actors);

        const wrapper = getWrapper(app, filmsAdmin);
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
        const filmsAdmin = useFilmsAdminStore();

        app.request = vi.fn()
            .mockImplementationOnce(() => json_free_actors_0)
            .mockImplementationOnce(() => json_film_actors_0);

        const wrapper = getWrapper(app, filmsAdmin);
        await flushPromises();
        
        expect(wrapper.text()).toContain('Актёры не добавлены');
        expect(wrapper.text()).toContain('Ничего не найдено');
    });
    
    it("Если список актёров null, появляются нужные записи", async () => {
        const app = useAppStore();
        const filmsAdmin = useFilmsAdminStore();

        app.request = vi.fn()
            .mockImplementationOnce(() => null)
            .mockImplementationOnce(() => null);

        const wrapper = getWrapper(app, filmsAdmin);
        await flushPromises();
        
        expect(wrapper.text()).toContain('Актёры не добавлены');
        expect(wrapper.text()).toContain('Ничего не найдено');
    });
    
    it("Повторный запрос на сервер для добавления актёра в фильм не отправляется", async () => {
        const app = useAppStore();
        // Компонента монтируется с условием, что запрос на сервер отправлен
        app.isRequest = true;
        const filmsAdmin = useFilmsAdminStore();

        app.request = vi.fn()
            .mockImplementationOnce(() => json_free_actors)
            .mockImplementationOnce(() => json_film_actors_0);

        const wrapper = getWrapper(app, filmsAdmin);
        await flushPromises();
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 1);
        
        const uls = wrapper.findAll('ul');
        expect(uls.length).toBe(1);
        
        const actorsUl = uls[0];
        const actorsLis = actorsUl.findAll('li');
        expect(actorsLis.length).toBe(7);
        // Отправляется запрос на сервер, добавляющий актёра в фильм
        expect(router.post).not.toHaveBeenCalled();
        await actorsLis[3].trigger('click');
        expect(router.post).not.toHaveBeenCalled();
    });
    
    it("Заполнение поля поиска актёров input отправляет запрос на сервер (проверка watch)", async () => {
        const app = useAppStore();
        const appRequest = vi.spyOn(app, 'request');
        const filmsAdmin = useFilmsAdminStore();

        const wrapper = getWrapper(app, filmsAdmin);
        
        appRequest.mockClear();
        expect(appRequest).not.toHaveBeenCalled();
        // После ввода одного символа отправляется запрос на сервер
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 1);
        checkInputField.checkInputFieldWhenThereIsNoRequest(inputFields[0], wrapper.vm.actorName, 'И');
        await flushPromises();
        expect(appRequest).toHaveBeenCalledTimes(1);
    });
});
