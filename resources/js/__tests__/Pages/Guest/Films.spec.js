import { mount } from "@vue/test-utils";
import { router } from '@inertiajs/vue3';

import { setActivePinia, createPinia } from 'pinia';
import Films from "@/Pages/Guest/Films.vue";
import GuestLayout from '@/Layouts/GuestLayout.vue';
import BreadCrumb from '@/Components/Elements/BreadCrumb.vue';
import Dropdown from '@/Components/Elements/Dropdown.vue';
import Buttons from '@/Components/Pagination/Buttons.vue';
import Info from '@/Components/Pagination/Info.vue';
import { useFilmsListStore } from '@/Stores/films';
import { useLanguagesListStore } from '@/Stores/languages';

import { films_0, films_10 } from '@/__tests__/data/films';
import { GuestLayoutStub } from '@/__tests__/stubs/layout';

// Делаем заглушку для Head
vi.mock('@inertiajs/vue3', async () => {
    const actual = await vi.importActual("@inertiajs/vue3");
    return {
        ...actual,
        router: {
            get: vi.fn()
        },
        Head: vi.fn()
    };
});

const getWrapper = function(films) {
    return mount(Films, {
            props: {
                errors: {},
                films
            },
            global: {
                stubs: {
                    GuestLayout: GuestLayoutStub
                },
                provide: {
                    filmsList: useFilmsListStore(),
                    languagesList: useLanguagesListStore()
                }
            }
        });
};

// Проверка названия страницы
const checkH1 = function(wrapper) {
    const h1 = wrapper.get('h1');
    expect(h1.text()).toBe('Фильмы');
};

// Проверка хлебных крошек
const checkBreadCrumb = function(wrapper) {
    // Отрисовываются хлебные крошки
    const breadCrumb = wrapper.findComponent(BreadCrumb);
    expect(breadCrumb.exists()).toBe(true);
    // Проверяем хлебные крошки
    const li = breadCrumb.findAll('li');
    expect(li.length).toBe(2);
    // Ссылка на страницу 'Главная страница'
    const a0 = li[0].find('a');
    expect(a0.attributes('href')).toBe('/guest');
    expect(a0.text()).toBe('Главная страница');
    // Название текущей страницы
    expect(li[1].find('a').exists()).toBe(false);
    expect(li[1].text()).toBe('Фильмы');
};

const checkTableHead = function(table) {
        // В шапке таблицы два ряда
        const thead = table.get('thead');
        expect(thead.isVisible()).toBe(true);
        const theadTr = thead.findAll('tr');
        expect(theadTr.length).toBe(2);
        
        // Первый ряд содержит заголовки
        const th0 = theadTr[0].findAll('th');
        expect(th0.length).toBe(5);
        expect(th0[0].text()).toBe('#');
        expect(th0[1].text()).toBe('Название');
        expect(th0[2].text()).toBe('Описание');
        expect(th0[4].text()).toBe('Год выхода');
        
        // Второй ряд содержит поля ввода
        const th1 = theadTr[1].findAll('th');
        expect(th1.length).toBe(5);
        expect(th1[0].text()).toBe('');
        expect(th1[1].get('input').element.value).toBe('');
        expect(th1[2].get('input').element.value).toBe('');
        expect(th1[3].text()).toBe('все');
        expect(th1[4].text()).toBe('все');
};

describe("@/Pages/Guest/Films.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    
    it("Отрисовка каталога фильмов", () => {
        const wrapper = getWrapper(films_10);
        
        expect(wrapper.findComponent(GuestLayout).exists()).toBe(true);
        
        // Проверяем, что текущая страница пагинации сохранена в filmsList
        expect(wrapper.vm.filmsList.page).toBe(films_10.current_page);
        
        // Отрисовывается кнопка изменения числа фильмов
        const dropdown = wrapper.getComponent(Dropdown);
        expect(dropdown.text()).toBe('Число фильмов на странице');
        
        checkH1(wrapper);
        
        checkBreadCrumb(wrapper);
        
        // Отрисовывается таблица фильмов
        const table = wrapper.get('table.container');
        expect(table.isVisible()).toBe(true);
        
        // Отрисовывается заголовок к таблице
        const info = table.get('caption').findComponent(Info);
        expect(info.text()).toBe(`Показано ${films_10.per_page} фильмов с ${films_10.from} по ${films_10.to} из ${films_10.total}`);
        
        checkTableHead(table);
        
        // Тело таблицы состоит из рядов с данными фильмов
        const tbody = table.get('tbody');
        expect(tbody.isVisible()).toBe(true);
        const tbodyTr = tbody.findAll('tr');
        expect(tbodyTr.length).toBe(films_10.per_page);
        const td3 = tbodyTr[3].findAll('td');
        expect(td3.length).toBe(5);
        expect(td3[0].text()).toBe(`${films_10.from + 3}`);
        expect(td3[1].text()).toBe(films_10.data[3].title);
        expect(td3[2].text()).toBe(films_10.data[3].description);
        expect(td3[3].text()).toBe(films_10.data[3].languageName);
        expect(td3[4].text()).toBe(films_10.data[3].releaseYear);
        
        // Отрисовываются кнопки пагинации
        const buttons = wrapper.findComponent(Buttons);
        expect(buttons.exists()).toBe(true);
    });
    
    it("Отрисовка каталога фильмов без фильмов", () => {
        const wrapper = getWrapper(films_0);
        
        expect(wrapper.findComponent(GuestLayout).exists()).toBe(true);
        
        // Проверяем, что текущая страница пагинации сохранена в filmsList
        expect(wrapper.vm.filmsList.page).toBe(films_0.current_page);
        
        // Отрисовывается кнопка изменения числа фильмов
        const dropdown = wrapper.getComponent(Dropdown);
        expect(dropdown.text()).toBe('Число фильмов на странице');
        
        checkH1(wrapper);
        
        checkBreadCrumb(wrapper);
        
        // Отрисовывается таблица фильмов
        const table = wrapper.get('table.container');
        expect(table.isVisible()).toBe(true);
        
        // Заголовок к таблице отсутствует
        expect(table.get('caption').findComponent(Info).exists()).toBe(false);
        
        checkTableHead(table);
        
        // Тело таблицы пустое
        const tbody = table.get('tbody');
        expect(tbody.isVisible()).toBe(true);
        const tbodyTr = tbody.findAll('tr');
        expect(tbodyTr.length).toBe(0);
        
        // Отсутствуют кнопки пагинации
        const buttons = wrapper.findComponent(Buttons);
        expect(buttons.exists()).toBe(false);
    });
    
    it("Изменение числа фильмов", async () => {
        const wrapper = getWrapper(films_10);
        
        expect(wrapper.findComponent(GuestLayout).exists()).toBe(true);
        
        // В начальный момент текущая страница films_10.current_page = 5
        expect(wrapper.vm.filmsList.page).toBe(films_10.current_page);
        // В начальный момент число фильмов на странице films_10.per_page = 10
        expect(wrapper.vm.filmsList.perPage).toBe(films_10.per_page);
        
        checkH1(wrapper);
        
        checkBreadCrumb(wrapper);
        
        // Находим кнопку для изменения числа фильмов
        const dropdown = wrapper.getComponent(Dropdown);
        const button = dropdown.get('button');
        
        // Список с выбором числа фильмов отсутствует
        expect(dropdown.find('ul').exists()).toBe(false);
        // Кликаем по кнопке
        await button.trigger('click');
        // Появляется список с выбором числа фильмов
        const ul = dropdown.find('ul');
        expect(ul.exists()).toBe(true);
        // Проверяем список с выбором числа фильмов
        const li = ul.findAll('li');
        expect(li.length).toBe(5);
        expect(li[0].text()).toBe('10');
        expect(li[1].text()).toBe('20');
        expect(li[2].text()).toBe('50');
        expect(li[3].text()).toBe('100');
        expect(li[4].text()).toBe('1000');
        
        // Запрос на сервер не отправлен
        expect(router.get).not.toHaveBeenCalled();
        // Кликаем по кнопке 50
        await li[2].trigger('click');
        // Отправляется запрос на сервер с правильным параметром
        expect(router.get).toHaveBeenCalledTimes(1);
        expect(router.get).toHaveBeenCalledWith(wrapper.vm.filmsList.getUrl('/guest/films'));
        // В filmsList изменяются текущая страница и число фильмов на странице
        expect(wrapper.vm.filmsList.page).toBe(1);
        expect(wrapper.vm.filmsList.perPage).toBe(50);
    });
    
    it("Задание фильтра для фильмов", async () => {
        vi.useFakeTimers();
        
        const wrapper = getWrapper(films_10);
        
        expect(wrapper.findComponent(GuestLayout).exists()).toBe(true);
        
        // Изменяется текущая страница с дефолтного 1 на films_10.current_page
        expect(wrapper.vm.filmsList.page).toBe(5);
        // Поля ввода пустые
        expect(wrapper.vm.filmsList.title).toBe('');
        expect(wrapper.vm.filmsList.description).toBe('');
        
        checkH1(wrapper);
        
        checkBreadCrumb(wrapper);
        
        // В шапке таблицы находим поля ввода
        const thead = wrapper.get('thead');
        const theadTr = thead.findAll('tr');
        const th1 = theadTr[1].findAll('th');
        expect(th1.length).toBe(5);
        expect(th1[0].text()).toBe('');
        expect(th1[1].get('input').element.value).toBe('');
        expect(th1[2].get('input').element.value).toBe('');
        expect(th1[3].text()).toBe('все');
        
        // Изменяем поле title
        th1[1].get('input').setValue('abc');
        expect(th1[1].get('input').element.value).toBe('abc');
        expect(wrapper.vm.filmsList.title).toBe('abc');
        
        // Изменяем поле description
        th1[2].get('input').setValue('xyz');
        expect(th1[2].get('input').element.value).toBe('xyz');
        expect(wrapper.vm.filmsList.description).toBe('xyz');
        
        // Нажимаем три клавиши, запрос отправляется один раз
        expect(router.get).not.toHaveBeenCalled();
        await th1[1].get('input').trigger('keyup', {key: 'a'});
        await th1[1].get('input').trigger('keyup', {key: 'b'});
        await th1[1].get('input').trigger('keyup', {key: 'c'});
        vi.advanceTimersByTime(2000);
        expect(router.get).toHaveBeenCalledTimes(1);
    });
    
    it("Функция setNewLanguageName изменяет languageName", async () => {
        const wrapper = getWrapper(films_10);
        
        expect(wrapper.vm.languageName).toBe('');
        wrapper.vm.setNewLanguageName('Новый язык');
        expect(wrapper.vm.languageName).toBe('Новый язык');
    });
    
    it("Функция setNewReleaseYear изменяет releaseYear", async () => {
        const wrapper = getWrapper(films_10);
        
        expect(wrapper.vm.releaseYear).toBe('');
        wrapper.vm.setNewReleaseYear('1970');
        expect(wrapper.vm.releaseYear).toBe('1970');
    });
});
