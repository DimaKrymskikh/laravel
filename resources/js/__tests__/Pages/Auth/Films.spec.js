import { mount } from "@vue/test-utils";
import { router } from '@inertiajs/vue3';

import '@/bootstrap';

import { setActivePinia, createPinia } from 'pinia';
import Films from "@/Pages/Auth/Films.vue";
import BreadCrumb from '@/Components/Elements/BreadCrumb.vue';
import Dropdown from '@/Components/Elements/Dropdown.vue';
import Buttons from '@/Components/Pagination/Buttons.vue';
import Info from '@/Components/Pagination/Info.vue';
import CheckCircleSvg from '@/Components/Svg/CheckCircleSvg.vue';
import PlusCircleSvg from '@/Components/Svg/PlusCircleSvg.vue';
import { useAppStore } from '@/Stores/app';
import { useFilmsListStore, useFilmsAccountStore } from '@/Stores/films';

import { films_0, films_10_user } from '@/__tests__/data/films';

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

describe("@/Pages/Auth/Films.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    afterEach(async () => {
        await router.get.mockClear();
        await router.post.mockClear();
    });
    
    it("Отрисовка каталога фильмов (залогиненный пользователь)", () => {
        const app = useAppStore();
        const filmsList = useFilmsListStore();
        const filmsAccount = useFilmsAccountStore();
        
        const wrapper = mount(Films, {
            props: {
                errors: null,
                films: films_10_user,
                user: {
                    id: 77,
                    is_admin: false
                }
            },
            global: {
                mocks: {
                    $page: {
                        component: 'Auth/Films'
                    }
                },
                provide: { app, filmsList, filmsAccount }
            }
        });
        
        // Проверяем, что текущая страница пагинации сохранена в filmsList
        expect(wrapper.vm.filmsList.page).toBe(films_10_user.current_page);
        
        // Отрисовывается кнопка изменения числа фильмов
        const dropdown = wrapper.getComponent(Dropdown);
        expect(dropdown.text()).toBe('Число фильмов на странице');
        
        // Отрисовывается заголовок страницы
        const h1 = wrapper.get('h1');
        expect(h1.text()).toBe('Каталог');
        
        // Отрисовываются хлебные крошки
        const breadCrumb = wrapper.findComponent(BreadCrumb);
        expect(breadCrumb.exists()).toBe(true);
        
        // Проверяем хлебные крошки
        const li = breadCrumb.findAll('li');
        expect(li.length).toBe(2);
        expect(li[0].find('a[href="/"]').exists()).toBe(true);
        expect(li[0].text()).toBe('Главная страница');
        expect(li[1].find('a').exists()).toBe(false);
        expect(li[1].text()).toBe('Каталог');
        
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
    });
    
    it("Отрисовка каталога фильмов без фильмов (залогиненный пользователь)", () => {
        const app = useAppStore();
        const filmsList = useFilmsListStore();
        const filmsAccount = useFilmsAccountStore();
        
        const wrapper = mount(Films, {
            props: {
                errors: null,
                films: films_0,
                user: {
                    id: 77,
                    is_admin: false
                }
            },
            global: {
                mocks: {
                    $page: {
                        component: 'Auth/Films'
                    }
                },
                provide: { app, filmsList, filmsAccount }
            }
        });
        
        // Проверяем, что текущая страница пагинации сохранена в filmsList
        expect(wrapper.vm.filmsList.page).toBe(films_0.current_page);
        
        // Отрисовывается кнопка изменения числа фильмов
        const dropdown = wrapper.getComponent(Dropdown);
        expect(dropdown.text()).toBe('Число фильмов на странице');
        
        // Отрисовывается заголовок страницы
        const h1 = wrapper.get('h1');
        expect(h1.text()).toBe('Каталог');
        
        // Отрисовываются хлебные крошки
        const breadCrumb = wrapper.findComponent(BreadCrumb);
        expect(breadCrumb.exists()).toBe(true);
        
        // Проверяем хлебные крошки
        const li = breadCrumb.findAll('li');
        expect(li.length).toBe(2);
        expect(li[0].find('a[href="/"]').exists()).toBe(true);
        expect(li[0].text()).toBe('Главная страница');
        expect(li[1].find('a').exists()).toBe(false);
        expect(li[1].text()).toBe('Каталог');
        
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
        const filmsAccount = useFilmsAccountStore();
        
        const wrapper = mount(Films, {
            props: {
                errors: null,
                films: films_10_user,
                user: {
                    id: 77,
                    is_admin: false
                }
            },
            global: {
                mocks: {
                    $page: {
                        component: 'Auth/Films'
                    }
                },
                provide: { app, filmsList, filmsAccount }
            }
        });
        
        // Изменяется текущая страница с дефолтного 1 на films_10.current_page
        expect(wrapper.vm.filmsList.page).toBe(5);
        // В начальный момент число фильмов на странице дефолтное
        expect(wrapper.vm.filmsList.perPage).toBe(20);
        
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
        expect(router.get).toHaveBeenCalledWith(wrapper.vm.filmsList.getUrl('/films'));
        // В filmsList изменяются текущая страница и число фильмов на странице
        expect(wrapper.vm.filmsList.page).toBe(1);
        expect(wrapper.vm.filmsList.perPage).toBe(50);
    });
    
    it("Задание фильтра для фильмов (залогиненный пользователь)", async () => {
        const app = useAppStore();
        const filmsList = useFilmsListStore();
        const filmsAccount = useFilmsAccountStore();
        
        const wrapper = mount(Films, {
            props: {
                errors: null,
                films: films_10_user,
                user: {
                    id: 77,
                    is_admin: false
                }
            },
            global: {
                mocks: {
                    $page: {
                        component: 'Auth/Films'
                    }
                },
                provide: { app, filmsList, filmsAccount }
            }
        });
        
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
        const filmsAccount = useFilmsAccountStore();
        
        const wrapper = mount(Films, {
            props: {
                errors: null,
                films: films_10_user,
                user: {
                    id: 77,
                    is_admin: false
                }
            },
            global: {
                mocks: {
                    $page: {
                        component: 'Auth/Films'
                    }
                },
                provide: { app, filmsList, filmsAccount }
            }
        });
        
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
        
        // title: 'Attacks Hate'
        const td3 = tbodyTr[3].findAll('td');
        // Отрисован плюс
        const plusCircleSvg = td3[4].findComponent(PlusCircleSvg);
        expect(plusCircleSvg.exists()).toBe(true);
        // Клик по плюсу отправляет запрос на сервер
        expect(router.post).not.toHaveBeenCalled();
        await plusCircleSvg.trigger('click');
        expect(router.post).toHaveBeenCalledTimes(1);
        
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
});
