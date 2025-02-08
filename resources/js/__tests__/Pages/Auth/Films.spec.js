import '@/bootstrap';
import { mount, flushPromises } from "@vue/test-utils";
import { router } from '@inertiajs/vue3';

import { setActivePinia, createPinia } from 'pinia';
import Films from "@/Pages/Auth/Films.vue";
import AuthLayout from '@/Layouts/AuthLayout.vue';
import BreadCrumb from '@/Components/Elements/BreadCrumb.vue';
import Dropdown from '@/Components/Elements/Dropdown.vue';
import Buttons from '@/Components/Pagination/Buttons.vue';
import Info from '@/Components/Pagination/Info.vue';
import CheckCircleSvg from '@/Components/Svg/CheckCircleSvg.vue';
import PlusCircleSvg from '@/Components/Svg/PlusCircleSvg.vue';
import Spinner from '@/components/Svg/Spinner.vue';
import EchoAuth from '@/Components/Broadcast/EchoAuth.vue';
import { useAppStore } from '@/Stores/app';
import { useFilmsListStore } from '@/Stores/films';
import { useGlobalConstsStore } from '@/Stores/globalConsts';

import { films_0, films_10_user } from '@/__tests__/data/films';
import { AuthLayoutStub } from '@/__tests__/stubs/layout';

// Делаем заглушку для Head
vi.mock('@inertiajs/vue3', async () => {
    const actual = await vi.importActual("@inertiajs/vue3");
    return {
        ...actual,
        router: {
            get: vi.fn(),
            post: vi.fn()
        },
        Head: vi.fn()
    };
});

const user = {
            id: 77,
            is_admin: false
        };

const getWrapper = function(app, filmsList, films, globalConsts) {
    return mount(Films, {
            props: {
                errors: {},
                films,
                user
            },
            global: {
                stubs: {
                    AuthLayout: AuthLayoutStub
                },
                provide: { app, filmsList, globalConsts }
            }
        });
};

describe("@/Pages/Auth/Films.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    
    it("Отрисовка каталога фильмов (залогиненный пользователь)", () => {
        const app = useAppStore();
        const filmsList = useFilmsListStore();
        const globalConsts = useGlobalConstsStore();
        
        const wrapper = getWrapper(app, filmsList, films_10_user, globalConsts);
        
        const authLayout = wrapper.getComponent(AuthLayout);
        expect(authLayout.props('user')).toStrictEqual(user);
        expect(authLayout.props('errors')).toStrictEqual({});
        
        // Проверяем, что текущая страница пагинации сохранена в filmsList
        expect(wrapper.vm.filmsList.page).toBe(films_10_user.current_page);
        
        // Отрисовывается кнопка изменения числа фильмов
        const dropdown = wrapper.getComponent(Dropdown);
        expect(dropdown.text()).toBe('Число фильмов на странице');
        
        // Отрисовывается заголовок страницы
        const h1 = wrapper.get('h1');
        expect(h1.text()).toBe('Каталог');
        
        // Отрисовываются хлебные крошки
        const breadCrumb = wrapper.getComponent(BreadCrumb);
        expect(breadCrumb.props('linksList')).toBe(wrapper.vm.linksList);
        
        // Отрисовывается таблица фильмов
        const table = wrapper.get('table.container');
        expect(table.isVisible()).toBe(true);
        
        // Отрисовывается заголовок к таблице
        const info = table.get('caption').findComponent(Info);
        expect(info.text()).toBe(`Показано ${films_10_user.per_page} фильмов с ${films_10_user.from} по ${films_10_user.to} из ${films_10_user.total}`);
        
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
        expect(th0[3].text()).toBe('Язык');
        expect(th0[4].text()).toBe('');
        
        // Второй ряд содержит поля ввода
        const th1 = theadTr[1].findAll('th');
        expect(th1.length).toBe(5);
        expect(th1[0].text()).toBe('');
        expect(th1[1].get('input').element.value).toBe('');
        expect(th1[2].get('input').element.value).toBe('');
        expect(th1[3].text()).toBe('');
        expect(th1[4].text()).toBe('');
        
        // Тело таблицы состоит из рядов с данными фильмов
        const tbody = table.get('tbody');
        expect(tbody.isVisible()).toBe(true);
        const tbodyTr = tbody.findAll('tr');
        expect(tbodyTr.length).toBe(films_10_user.per_page);
        
        // title: 'Attacks Hate'
        const td3 = tbodyTr[3].findAll('td');
        expect(td3.length).toBe(5);
        expect(td3[0].text()).toBe(`${films_10_user.from + 3}`);
        expect(td3[1].text()).toBe(films_10_user.data[3].title);
        expect(td3[2].text()).toBe(films_10_user.data[3].description);
        expect(td3[3].text()).toBe(films_10_user.data[3].language.name);
        // Отрисован плюс
        expect(td3[4].findComponent(PlusCircleSvg).exists()).toBe(true);
        
        // title: 'Attraction Newton'
        const td4 = tbodyTr[4].findAll('td');
        expect(td4.length).toBe(5);
        expect(td4[0].text()).toBe(`${films_10_user.from + 4}`);
        expect(td4[1].text()).toBe(films_10_user.data[4].title);
        expect(td4[2].text()).toBe(films_10_user.data[4].description);
        expect(td4[3].text()).toBe(films_10_user.data[4].language.name);
        // Отрисована галочка
        expect(td4[4].findComponent(CheckCircleSvg).exists()).toBe(true);
        
        // Отрисовываются кнопки пагинации
        const buttons = wrapper.findComponent(Buttons);
        expect(buttons.exists()).toBe(true);
        
        const echoAuth = wrapper.getComponent(EchoAuth);
        expect(echoAuth.props('user')).toStrictEqual(user);
        expect(echoAuth.props('events')).toStrictEqual(['AddFilmInUserList']);
    });
    
    it("Отрисовка каталога фильмов без фильмов (залогиненный пользователь)", () => {
        const app = useAppStore();
        const filmsList = useFilmsListStore();
        const globalConsts = useGlobalConstsStore();
        
        const wrapper = getWrapper(app, filmsList, films_0, globalConsts);
        
        const authLayout = wrapper.getComponent(AuthLayout);
        expect(authLayout.props('user')).toStrictEqual(user);
        expect(authLayout.props('errors')).toStrictEqual({});
        
        // Проверяем, что текущая страница пагинации сохранена в filmsList
        expect(wrapper.vm.filmsList.page).toBe(films_0.current_page);
        
        // Отрисовывается кнопка изменения числа фильмов
        const dropdown = wrapper.getComponent(Dropdown);
        expect(dropdown.text()).toBe('Число фильмов на странице');
        
        // Отрисовывается заголовок страницы
        const h1 = wrapper.get('h1');
        expect(h1.text()).toBe('Каталог');
        
        // Отрисовываются хлебные крошки
        const breadCrumb = wrapper.getComponent(BreadCrumb);
        expect(breadCrumb.props('linksList')).toBe(wrapper.vm.linksList);
        
        // Отрисовывается таблица фильмов
        const table = wrapper.get('table.container');
        expect(table.isVisible()).toBe(true);
        
        // Заголовок к таблице отсутствует
        expect(table.get('caption').findComponent(Info).exists()).toBe(false);
        
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
        expect(th0[3].text()).toBe('Язык');
        expect(th0[4].text()).toBe('');
        
        // Второй ряд содержит поля ввода
        const th1 = theadTr[1].findAll('th');
        expect(th1.length).toBe(5);
        expect(th1[0].text()).toBe('');
        expect(th1[1].get('input').element.value).toBe('');
        expect(th1[2].get('input').element.value).toBe('');
        expect(th1[3].text()).toBe('');
        expect(th1[4].text()).toBe('');
        
        // Тело таблицы пустое
        const tbody = table.get('tbody');
        expect(tbody.isVisible()).toBe(true);
        const tbodyTr = tbody.findAll('tr');
        expect(tbodyTr.length).toBe(0);
        
        // Отсутствуют кнопки пагинации
        const buttons = wrapper.findComponent(Buttons);
        expect(buttons.exists()).toBe(false);
    });
    
    it("Изменение числа фильмов (залогиненный пользователь)", async () => {
        const app = useAppStore();
        const filmsList = useFilmsListStore();
        const globalConsts = useGlobalConstsStore();
        
        const wrapper = getWrapper(app, filmsList, films_10_user, globalConsts);
        
        // В начальный момент текущая страница films_10_user.current_page = 5
        expect(wrapper.vm.filmsList.page).toBe(films_10_user.current_page);
        // В начальный момент число фильмов на странице films_10_user.per_page = 10
        expect(wrapper.vm.filmsList.perPage).toBe(films_10_user.per_page);
        
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
        expect(router.get).toHaveBeenCalledWith(wrapper.vm.filmsList.getUrl('/films'));
        // В filmsList изменяются текущая страница и число фильмов на странице
        expect(wrapper.vm.filmsList.page).toBe(1);
        expect(wrapper.vm.filmsList.perPage).toBe(50);
    });
    
    it("Задание фильтра для фильмов (залогиненный пользователь)", async () => {
        const app = useAppStore();
        const filmsList = useFilmsListStore();
        const globalConsts = useGlobalConstsStore();
        
        const wrapper = getWrapper(app, filmsList, films_10_user, globalConsts);
        
        // Изменяется текущая страница с дефолтного 1 на films_10.current_page
        expect(wrapper.vm.filmsList.page).toBe(5);
        // Поля ввода пустые
        expect(wrapper.vm.filmsList.title).toBe('');
        expect(wrapper.vm.filmsList.description).toBe('');
        
        // В шапке таблицы находим поля ввода
        const thead = wrapper.get('thead');
        const theadTr = thead.findAll('tr');
        const th1 = theadTr[1].findAll('th');
        expect(th1.length).toBe(5);
        expect(th1[0].text()).toBe('');
        expect(th1[1].get('input').element.value).toBe('');
        expect(th1[2].get('input').element.value).toBe('');
        expect(th1[3].text()).toBe('');
        expect(th1[4].text()).toBe('');
        
        // Изменяем поле title
        th1[1].get('input').setValue('abc');
        expect(th1[1].get('input').element.value).toBe('abc');
        expect(wrapper.vm.filmsList.title).toBe('abc');
        
        // Изменяем поле description
        th1[2].get('input').setValue('xyz');
        expect(th1[2].get('input').element.value).toBe('xyz');
        expect(wrapper.vm.filmsList.description).toBe('xyz');
        
        // Клик по кнопке 'a' не отправляет запрос на сервер
        expect(router.get).not.toHaveBeenCalled();
        await th1[1].get('input').trigger('keyup', {key: 'a'});
        expect(router.get).not.toHaveBeenCalled();
        
        // Клик по кнопке 'enter' отправляет запрос на сервер с правильным параметром
        await th1[1].get('input').trigger('keyup', {key: "enter"});
        expect(router.get).toHaveBeenCalledTimes(1);
        expect(router.get).toHaveBeenCalledWith(wrapper.vm.filmsList.getUrl('/films'));
        // Текущая страница становится 1
        expect(wrapper.vm.filmsList.page).toBe(1);
    });
    
    it("Добавление фильма в коллекцию пользователя", async () => {
        const app = useAppStore();
        const filmsList = useFilmsListStore();
        const globalConsts = useGlobalConstsStore();
        
        const wrapper = getWrapper(app, filmsList, films_10_user, globalConsts);
        
        // Изменяется текущая страница с дефолтного 1 на films_10.current_page
        expect(wrapper.vm.filmsList.page).toBe(5);
        
        // Тело таблицы состоит из рядов с данными фильмов
        const table = wrapper.get('table.container');
        // Клик по тегу без родителя <td> не ломает функцию handlerTableChange
        expect(router.post).not.toHaveBeenCalled();
        await table.trigger('click');
        expect(router.post).not.toHaveBeenCalled();
        
        const tbody = table.get('tbody');
        const tbodyTr = tbody.findAll('tr');
        
        // films_10_user.data[3]
        // title: 'Attacks Hate'
        const td3 = tbodyTr[3].findAll('td');
        // Отрисован плюс
        const plusCircleSvg = td3[4].findComponent(PlusCircleSvg);
        expect(plusCircleSvg.exists()).toBe(true);
        // Клик по плюсу отправляет запрос на сервер
        expect(router.post).not.toHaveBeenCalled();
        await plusCircleSvg.trigger('click');
        expect(router.post).toHaveBeenCalledTimes(1);
        // router.post вызывается с нужными параметрами
        expect(router.post).toHaveBeenCalledWith(filmsList.getUrl(`userfilms/addfilm/${films_10_user.data[3].id}`), {}, {
            preserveScroll: true,
            onBefore: expect.anything(),
            onFinish: expect.anything()
        });
        
        router.post.mockClear();
        
        // title: 'Attraction Newton'
        const td4 = tbodyTr[4].findAll('td');
        // Отрисована галочка
        const checkCircleSvg = td4[4].findComponent(CheckCircleSvg);
        expect(td4[4].findComponent(CheckCircleSvg).exists()).toBe(true);
        // Клик по галочке не отправляет запрос на сервер
        expect(router.post).not.toHaveBeenCalled();
        await plusCircleSvg.trigger('click');
        expect(router.post).not.toHaveBeenCalled();
    });
    
    it("Нельзя отправить запрос на добавление фильма, если app.isRequest = true", async () => {
        const app = useAppStore();
        app.isRequest = true;
        const filmsList = useFilmsListStore();
        const globalConsts = useGlobalConstsStore();
        
        const wrapper = getWrapper(app, filmsList, films_10_user, globalConsts);
        
        // В таблице находим фильм с "+"
        const table = wrapper.get('table.container');
        const tbody = table.get('tbody');
        const tbodyTr = tbody.findAll('tr');
        
        // title: 'Attacks Hate'
        const td3 = tbodyTr[3].findAll('td');
        // Отрисован плюс
        const plusCircleSvg = td3[4].findComponent(PlusCircleSvg);
        expect(plusCircleSvg.exists()).toBe(true);
        // Клик по плюсу не отправляет запрос на сервер
        await plusCircleSvg.trigger('click');
        expect(router.post).not.toHaveBeenCalled();
    });
    
    it("Проверка появления спинера", async () => {
        const app = useAppStore();
        const filmsList = useFilmsListStore();
        const globalConsts = useGlobalConstsStore();
        
        const wrapper = getWrapper(app, filmsList, films_10_user);
        
        expect(wrapper.vm.app.isRequest).toBe(false);
        
        const tbody = wrapper.get('table').get('tbody');
        const tbodyTr = tbody.findAll('tr');
        expect(tbodyTr.length).toBe(10);
        // Во всех строках таблицы спиннер отсутствует
        expect(tbodyTr[0].findComponent(Spinner).exists()).toBe(false);
        expect(tbodyTr[3].findComponent(Spinner).exists()).toBe(false);
        expect(tbodyTr[4].findComponent(Spinner).exists()).toBe(false);
        
        // Attacks Hate
        const td0 = tbodyTr[3].findAll('td');
      
        const spanPlusCircleSvg = td0[4].getComponent(PlusCircleSvg).get('span');
        // Клик по spanPlusCircleSvg приводит к вызову функции addCity
        wrapper.vm.addFilm(spanPlusCircleSvg.element);
        // Правильно находиться id фильма
        expect(wrapper.vm.filmId).toBe(String(films_10_user.data[3].id));
        // В router.post должен измениться app.isRequest
        wrapper.vm.app.isRequest = true;
        
        await flushPromises();
        
        // Спиннер появился только в одной строке
        expect(tbodyTr[0].findComponent(Spinner).exists()).toBe(false);
        expect(tbodyTr[3].findComponent(Spinner).exists()).toBe(true);
        expect(tbodyTr[4].findComponent(Spinner).exists()).toBe(false);
    });
    
    it("Проверка функции onBeforeForAddFilm", () => {
        const app = useAppStore();
        // По умолчанию
        expect(app.isRequest).toBe(false);
        const filmsList = useFilmsListStore();
        const globalConsts = useGlobalConstsStore();
        
        const wrapper = getWrapper(app, filmsList, films_10_user, globalConsts);
        wrapper.vm.onBeforeForAddFilm();
        
        expect(app.isRequest).toBe(true);
    });
    
    it("Проверка функции onFinishForAddFilm", async () => {
        const app = useAppStore();
        app.isRequest = true;
        const filmsList = useFilmsListStore();
        const globalConsts = useGlobalConstsStore();
        
        const wrapper = getWrapper(app, filmsList, films_10_user, globalConsts);
        wrapper.vm.onFinishForAddFilm();
        
        expect(app.isRequest).toBe(false);
    });
});
