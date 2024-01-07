import { mount, flushPromises } from "@vue/test-utils";
import { router } from '@inertiajs/vue3';
import { setActivePinia, createPinia } from 'pinia';

import Films from "@/Pages/Admin/Films.vue";
import AddFilmBlock from '@/Components/Pages/Admin/Films/AddFilmBlock.vue';
import RemoveFilmModal from '@/Components/Modal/Request/Films/RemoveFilmModal.vue';
import UpdateFilmModal from '@/Components/Modal/Request/Films/UpdateFilmModal.vue';
import UpdateFilmActorsBlock from '@/Components/Pages/Admin/Films/UpdateFilmActorsBlock.vue';
import UpdateFilmLanguageModal from '@/Components/Modal/Request/Films/UpdateFilmLanguageModal.vue';
import UpdateFilmActorsModal from '@/Components/Modal/Request/Films/UpdateFilmActorsModal.vue';
import BreadCrumb from '@/Components/Elements/BreadCrumb.vue';
import Dropdown from '@/Components/Elements/Dropdown.vue';
import Buttons from '@/Components/Pagination/Buttons.vue';
import Info from '@/Components/Pagination/Info.vue';
import PencilSvg from '@/Components/Svg/PencilSvg.vue';
import TrashSvg from '@/Components/Svg/TrashSvg.vue';
import { useFilmsAdminStore } from '@/Stores/films';

import { films_10, films_0 } from '@/__tests__/data/films';
import { AdminLayoutStub } from '@/__tests__/stubs/layout';

// Делаем заглушку для Head
vi.mock('@inertiajs/vue3', async () => {
    const actual = await vi.importActual("@inertiajs/vue3");
    return {
        ...actual,
        Head: vi.fn(),
        router: {
            get: vi.fn()
        }
    };
});

const getWrapper = function(films, filmsAdmin) {
    return mount(Films, {
            props: {
                errors: {},
                films
            },
            global: {
                stubs: {
                    AdminLayout: AdminLayoutStub,
                    UpdateFilmModal: true,
                    UpdateFilmLanguageModal: true,
                    RemoveFilmModal: true,
                    UpdateFilmActorsModal: true
                },
                provide: { filmsAdmin }
            }
        });
};

const recordWhenTheFilmsTableIsEmpty = 'Ни один фильм не найден, или ещё ни один фильм не добавлен.';
    
const checkH1 = function(wrapper) {
    const h1 = wrapper.get('h1');
    expect(h1.text()).toBe(wrapper.vm.titlePage);
};

const checkBreadCrumb = function(wrapper) {
    const breadCrumb = wrapper.getComponent(BreadCrumb);
    expect(breadCrumb.isVisible()).toBe(true);
    const li = breadCrumb.findAll('li');
    expect(li.length).toBe(2);
    expect(li[0].find('a[href="/admin"]').exists()).toBe(true);
    expect(li[0].text()).toBe('Страница админа');
    expect(li[1].find('a').exists()).toBe(false);
    expect(li[1].text()).toBe(wrapper.vm.titlePage);
};

const checkElements = function(wrapper) {
    const dropdown = wrapper.getComponent(Dropdown);
    expect(dropdown.props('buttonName')).toBe('Число фильмов на странице');
    expect(dropdown.props('itemsNumberOnPage')).toBe(wrapper.vm.films.per_page);
    expect(dropdown.props('changeNumber')).toBe(wrapper.vm.changeNumberOfFilmsOnPage);

    expect(wrapper.getComponent(AddFilmBlock).isVisible()).toBe(true);
};

describe("@/Pages/Admin/Films.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    
    it("Отрисовка страницы 'Фильмы' при наличии фильмов", () => {
        const filmsAdmin = useFilmsAdminStore();
        
        const wrapper = getWrapper(films_10, filmsAdmin);
        
        // Проверяем заголовок
        checkH1(wrapper);
        
        // Проверяем хлебные крошки
        checkBreadCrumb(wrapper);
        
        checkElements(wrapper);
        
        // Проверяем таблицу актёров
        const table = wrapper.get('table');
        expect(table.isVisible()).toBe(true);
        // На странице отсутствует запись
        expect(wrapper.text()).not.toContain(recordWhenTheFilmsTableIsEmpty);
        
        const caption = table.get('caption');
        const info = caption.getComponent(Info);
        expect(info.props('films')).toBe(wrapper.vm.films);
        
        // Проверяем заголовок таблицы городов
        const thead = table.get('thead');
        expect(thead.isVisible()).toBe(true);
        const trHead = thead.findAll('tr');
        expect(trHead.length).toBe(2);
        const th0 = trHead[0].findAll('th');
        expect(th0.length).toBe(7);
        expect(th0[0].text()).toBe('#');
        expect(th0[1].text()).toBe('Название');
        expect(th0[2].text()).toBe('Описание');
        expect(th0[3].text()).toBe('Актёры');
        expect(th0[4].text()).toBe('Год выхода');
        expect(th0[5].text()).toBe('Язык');
        expect(th0[6].text()).toBe('');
        
        const th1 = trHead[1].findAll('th');
        expect(th1.length).toBe(7);
        expect(th1[0].text()).toBe('');
        expect(th1[1].get('input').element.value).toBe('');
        expect(th1[2].get('input').element.value).toBe('');
        expect(th1[3].text()).toBe('');
        expect(th1[4].get('input').element.value).toBe('');
        expect(th1[5].text()).toBe('');
        expect(th1[6].text()).toBe('');
        
        // Проверяем тело таблицы фильмов 
        const tbody = table.get('tbody');
        expect(tbody.isVisible()).toBe(true);
        const tr = tbody.findAll('tr');
        expect(tr.length).toBe(10);
        
        const tds = tr[3].findAll('td');
        expect(tds.length).toBe(12);
        // films_10.from = 41 (исследуем 4-ю строку, tr[3])
        expect(tds[0].text()).toBe('44');
        expect(tds[1].text()).toBe(films_10.data[3].title);
        expect(tds[2].getComponent(PencilSvg).props('title')).toBe('Изменить название фильма');
        expect(tds[3].text()).toBe(films_10.data[3].description);
        expect(tds[4].getComponent(PencilSvg).props('title')).toBe('Изменить описание фильма');
        expect(tds[5].text()).toBe('');
        expect(tds[6].getComponent(PencilSvg).props('title')).toBe('Изменить список актёров');
        expect(tds[7].text()).toBe('');
        expect(tds[8].getComponent(PencilSvg).props('title')).toBe('Изменить год выхода фильма');
        expect(tds[9].text()).toBe(films_10.data[3].language.name);
        expect(tds[10].getComponent(PencilSvg).props('title')).toBe('Изменить язык фильма');
        expect(tds[11].getComponent(TrashSvg).props('title')).toBe('Удалить фильм');
        
        const buttons = wrapper.getComponent(Buttons);
        expect(buttons.props('links')).toStrictEqual(films_10.links);
    });
    
    it("Отрисовка страницы 'Фильмы' без фильмов", () => {
        const filmsAdmin = useFilmsAdminStore();
        
        const wrapper = getWrapper(films_0, filmsAdmin);
        
        // Проверяем заголовок
        checkH1(wrapper);
        
        // Проверяем хлебные крошки
        checkBreadCrumb(wrapper);
        
        checkElements(wrapper);
        
        // Таблица пустая
        const table = wrapper.get('table');
        // Нет подписи таблицы
        const caption = table.get('caption');
        expect(caption.findComponent(Info).exists()).toBe(false);
        // Присутствует заголовок таблицы
        expect(table.find('thead').exists()).toBe(true);
        // В теле таблицы нет ни одной строки
        const tbody = table.get('tbody');
        const trs = tbody.findAll('tr');
        expect(trs.length).toBe(0);
        // На странице присутствует запись
        expect(wrapper.text()).toContain(recordWhenTheFilmsTableIsEmpty);
        
        expect(wrapper.findComponent(Buttons).exists()).toBe(false);
    });
    
    it("Проверка работы фильтров", async () => {
        const filmsAdmin = useFilmsAdminStore();
        // films_10.per_page не равен дефолтному filmsAdmin.perPage
        filmsAdmin.perPage = films_10.per_page;
        
        const wrapper = getWrapper(films_10, filmsAdmin);
        
        // Запрос не отправлен
        expect(router.get).not.toHaveBeenCalled();
        
        // Начальные данные
        expect(wrapper.vm.filmsAdmin.page).toBe(films_10.current_page);
        expect(wrapper.vm.filmsAdmin.perPage).toBe(films_10.per_page);
        expect(wrapper.vm.filmsAdmin.title).toBe('');
        expect(wrapper.vm.filmsAdmin.description).toBe('');
        expect(wrapper.vm.filmsAdmin.release_year).toBe('');
        
        const thead = wrapper.get('thead');
        // Берём 2-й ряд
        const tr2 = thead.findAll('tr')[1];
        const ths = tr2.findAll('th');
        const inputTitle = ths[1].get('input');
        const inputDescription = ths[2].get('input');
        const inputReleaseYear = ths[4].get('input');
        
        // Проверяем v-model
        await inputTitle.setValue('Название');
        expect(wrapper.vm.filmsAdmin.title).toBe('Название');
        await inputDescription.setValue('Описание');
        expect(wrapper.vm.filmsAdmin.description).toBe('Описание');
        await inputReleaseYear.setValue('2024');
        expect(wrapper.vm.filmsAdmin.release_year).toBe('2024');
        
        await inputDescription.trigger('keyup', {key: 'a'});
        // Запрос не отправлен
        expect(router.get).not.toHaveBeenCalled();
        
        await inputDescription.trigger('keyup', {key: 'enter'});
        // Активная страница становится первой, и отправляется запрос с нужными параметрами
        expect(wrapper.vm.filmsAdmin.page).toBe(1);
        expect(router.get).toHaveBeenCalledWith(wrapper.vm.filmsAdmin.getUrl('/admin/films'));
    });
    
    it("Изменение числа фильмов на странице", async () => {
        const filmsAdmin = useFilmsAdminStore();
        filmsAdmin.perPage = films_10.per_page;
        
        const wrapper = getWrapper(films_10, filmsAdmin);
        
        expect(wrapper.vm.filmsAdmin.page).toBe(films_10.current_page);
        expect(wrapper.vm.filmsAdmin.perPage).toBe(films_10.per_page);
        
        checkH1(wrapper);
        
        checkBreadCrumb(wrapper);
        
        // Находим кнопку для изменения числа фильмов
        const dropdown = wrapper.getComponent(Dropdown);
        const button = dropdown.get('button');
        
        // Список с выбором числа элементов отсутствует
        expect(dropdown.find('ul').exists()).toBe(false);
        // Кликаем по кнопке
        await button.trigger('click');
        // Появляется список с выбором числа элементов
        const ul = dropdown.find('ul');
        expect(ul.exists()).toBe(true);
        // Проверяем список с выбором числа элементов
        const li = ul.findAll('li');
        expect(li.length).toBe(4);
        expect(li[0].text()).toBe('10');
        expect(li[1].text()).toBe('20');
        expect(li[2].text()).toBe('50');
        expect(li[3].text()).toBe('100');
        
        // Запрос на сервер не отправлен
        expect(router.get).not.toHaveBeenCalled();
        // Кликаем по кнопке 50
        await li[2].trigger('click');
        // Отправляется запрос на сервер с правильным параметром
        expect(router.get).toHaveBeenCalledTimes(1);
        expect(router.get).toHaveBeenCalledWith(wrapper.vm.filmsAdmin.getUrl('/admin/films'));
        // В filmsAdmin изменяются текущая страница и число фильмов на странице
        expect(wrapper.vm.filmsAdmin.page).toBe(1);
        expect(wrapper.vm.filmsAdmin.perPage).toBe(50);
    });
    
    it("Проверка появления модальных окон", async () => {
        const filmsAdmin = useFilmsAdminStore();
        
        const wrapper = getWrapper(films_10, filmsAdmin);
        
        // Находим таблицу
        const table = wrapper.get('table');
        expect(table.isVisible()).toBe(true);
        
        // В теле таблицы берём одну строку
        const tbody = table.get('tbody');
        expect(tbody.isVisible()).toBe(true);
        const trs = tbody.findAll('tr');
        expect(trs.length).toBe(10);
        const activeTr = trs[1];
        
        const tds = activeTr.findAll('td');
        expect(tds.length).toBe(12);
        const pencilTitle = tds[2].getComponent(PencilSvg);
        const pencilDescription = tds[4].getComponent(PencilSvg);
        const pencilActorsList = tds[6].getComponent(PencilSvg);
        const pencilReleaseYear = tds[8].getComponent(PencilSvg);
        const pencilLanguage = tds[10].getComponent(PencilSvg);
        const trashSvg = tds[11].getComponent(TrashSvg);
        
        expect(wrapper.findComponent(UpdateFilmModal).exists()).toBe(false);
        await pencilTitle.trigger('click');
        expect(wrapper.findComponent(UpdateFilmModal).exists()).toBe(true);
        expect(wrapper.vm.field).toBe('title');
        // Закрываем вручную модальное окно
        wrapper.vm.isShowUpdateFilmModal = false;
        await flushPromises();
        
        expect(wrapper.findComponent(UpdateFilmModal).exists()).toBe(false);
        await pencilDescription.trigger('click');
        expect(wrapper.findComponent(UpdateFilmModal).exists()).toBe(true);
        expect(wrapper.vm.field).toBe('description');
        // Закрываем вручную модальное окно
        wrapper.vm.isShowUpdateFilmModal = false;
        await flushPromises();
        
        expect(wrapper.findComponent(UpdateFilmActorsModal).exists()).toBe(false);
        await pencilActorsList.trigger('click');
        expect(wrapper.findComponent(UpdateFilmActorsModal).exists()).toBe(true);
        // Закрываем вручную модальное окно
        wrapper.vm.isShowUpdateFilmActorsModal = false;
        await flushPromises();
        
        expect(wrapper.findComponent(UpdateFilmModal).exists()).toBe(false);
        await pencilReleaseYear.trigger('click');
        expect(wrapper.findComponent(UpdateFilmModal).exists()).toBe(true);
        expect(wrapper.vm.field).toBe('release_year');
        // Закрываем вручную модальное окно
        wrapper.vm.isShowUpdateFilmModal = false;
        await flushPromises();
        
        expect(wrapper.findComponent(UpdateFilmLanguageModal).exists()).toBe(false);
        await pencilLanguage.trigger('click');
        expect(wrapper.findComponent(UpdateFilmLanguageModal).exists()).toBe(true);
        // Закрываем вручную модальное окно
        wrapper.vm.isShowUpdateFilmLanguageModal = false;
        await flushPromises();
        
        expect(wrapper.findComponent(RemoveFilmModal).exists()).toBe(false);
        await trashSvg.trigger('click');
        expect(wrapper.findComponent(RemoveFilmModal).exists()).toBe(true);
        // Закрываем вручную модальное окно
        wrapper.vm.isShowRemoveFilmModal = false;
        await flushPromises();
    });
    
    it("Функция hideUpdateFilmModal изменяет isShowUpdateFilmModal с true на false", () => {
        const filmsAdmin = useFilmsAdminStore();
        const wrapper = getWrapper(films_10, filmsAdmin);
        
        wrapper.vm.isShowUpdateFilmModal = true;
        wrapper.vm.hideUpdateFilmModal();
        expect(wrapper.vm.isShowUpdateFilmModal).toBe(false);
    });
    
    it("Функция hideRemoveFilmModal изменяет isShowRemoveFilmModal с true на false", () => {
        const filmsAdmin = useFilmsAdminStore();
        const wrapper = getWrapper(films_10, filmsAdmin);
        
        wrapper.vm.isShowRemoveFilmModal = true;
        wrapper.vm.hideRemoveFilmModal();
        expect(wrapper.vm.isShowRemoveFilmModal).toBe(false);
    });
    
    it("Функция hideUpdateFilmActorsModal изменяет isShowUpdateFilmActorsModal с true на false", () => {
        const filmsAdmin = useFilmsAdminStore();
        const wrapper = getWrapper(films_10, filmsAdmin);
        
        wrapper.vm.isShowUpdateFilmActorsModal = true;
        wrapper.vm.hideUpdateFilmActorsModal();
        expect(wrapper.vm.isShowUpdateFilmActorsModal).toBe(false);
    });
    
    it("Функция hideUpdateFilmLanguageModal изменяет isShowUpdateFilmLanguageModal с true на false", () => {
        const filmsAdmin = useFilmsAdminStore();
        const wrapper = getWrapper(films_10, filmsAdmin);
        
        wrapper.vm.isShowUpdateFilmLanguageModal = true;
        wrapper.vm.hideUpdateFilmLanguageModal();
        expect(wrapper.vm.isShowUpdateFilmLanguageModal).toBe(false);
    });
});
