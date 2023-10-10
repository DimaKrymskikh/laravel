import { mount } from "@vue/test-utils";
import { router } from '@inertiajs/vue3';

import '@/bootstrap';

import { setActivePinia, createPinia } from 'pinia';
import Account from "@/Pages/Auth/Account.vue";
import BreadCrumb from '@/Components/Elements/BreadCrumb.vue';
import Dropdown from '@/Components/Elements/Dropdown.vue';
import Buttons from '@/Components/Pagination/Buttons.vue';
import Info from '@/Components/Pagination/Info.vue';
import EyeSvg from '@/Components/Svg/EyeSvg.vue';
import TrashSvg from '@/Components/Svg/TrashSvg.vue';
import AccountRemoveBlock from '@/Pages/Auth/Account/AccountRemoveBlock.vue';
import AdminBlock from '@/Pages/Auth/Account/AdminBlock.vue';
import FilmRemoveModal from '@/Components/Modal/Request/FilmRemoveModal.vue';
import { useAppStore } from '@/Stores/app';
import { useFilmsListStore, useFilmsAccountStore } from '@/Stores/films';

import { films_10_user } from '@/__tests__/data/films';

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

describe("@/Pages/Auth/Account.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    afterEach(async () => {
        await router.get.mockClear();
        await router.post.mockClear();
    });
    
    it("Отрисовка ЛК", () => {
        const app = useAppStore();
        const filmsList = useFilmsListStore();
        const filmsAccount = useFilmsAccountStore();
        
        const wrapper = mount(Account, {
            props: {
                errors: null,
                films: films_10_user,
                user: {
                    id: 77,
                    is_admin: false,
                    login: 'TestLogin'
                }
            },
            global: {
                mocks: {
                    $page: {
                        component: 'Auth/Account'
                    }
                },
                provide: { app, filmsList, filmsAccount }
            }
        });
        
        // Проверяем, что текущая страница пагинации сохранена в filmsList
        expect(wrapper.vm.filmsAccount.page).toBe(films_10_user.current_page);
        
        // Отрисовывается кнопка изменения числа фильмов
        const dropdown = wrapper.getComponent(Dropdown);
        expect(dropdown.text()).toBe('Число фильмов на странице');
        
        expect(wrapper.findComponent(AccountRemoveBlock).exists()).toBe(true);
        
        expect(wrapper.findComponent(AdminBlock).exists()).toBe(true);
        
        // Отрисовывается заголовок страницы
        const h1 = wrapper.get('h1');
        expect(h1.text()).toBe('Добрый день, TestLogin');
        
        // Отрисовываются хлебные крошки
        const breadCrumb = wrapper.findComponent(BreadCrumb);
        expect(breadCrumb.exists()).toBe(true);
        
        // Проверяем хлебные крошки
        const li = breadCrumb.findAll('li');
        expect(li.length).toBe(2);
        expect(li[0].find('a[href="/"]').exists()).toBe(true);
        expect(li[0].text()).toBe('Главная страница');
        expect(li[1].find('a').exists()).toBe(false);
        expect(li[1].text()).toBe('ЛК');
        
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
        expect(th0.length).toBe(6);
        expect(th0[0].text()).toBe('#');
        expect(th0[1].text()).toBe('Название');
        expect(th0[2].text()).toBe('Описание');
        expect(th0[3].text()).toBe('Язык');
        expect(th0[4].text()).toBe('');
        expect(th0[5].text()).toBe('');
        
        // Второй ряд содержит поля ввода
        const th1 = theadTr[1].findAll('th');
        expect(th1.length).toBe(6);
        expect(th1[0].text()).toBe('');
        expect(th1[1].get('input').element.value).toBe('');
        expect(th1[2].get('input').element.value).toBe('');
        expect(th1[3].text()).toBe('');
        expect(th1[4].text()).toBe('');
        expect(th1[5].text()).toBe('');
        
        // Тело таблицы состоит из рядов с данными фильмов
        const tbody = table.get('tbody');
        expect(tbody.isVisible()).toBe(true);
        const tbodyTr = tbody.findAll('tr');
        expect(tbodyTr.length).toBe(films_10_user.per_page);
        
        // Отрисовка одного ряда тела таблицы
        const td3 = tbodyTr[3].findAll('td');
        expect(td3.length).toBe(6);
        expect(td3[0].text()).toBe(`${films_10_user.from + 3}`);
        expect(td3[1].text()).toBe(films_10_user.data[3].title);
        expect(td3[2].text()).toBe(films_10_user.data[3].description);
        expect(td3[3].text()).toBe(films_10_user.data[3].language.name);
        expect(td3[4].findComponent(EyeSvg).exists()).toBe(true);
        expect(td3[5].findComponent(TrashSvg).exists()).toBe(true);
        
        // Отрисовываются кнопки пагинации
        const buttons = wrapper.findComponent(Buttons);
        expect(buttons.exists()).toBe(true);
    });
    
    it("Показать/скрыть модальное окно удаления фильма", async () => {
        const app = useAppStore();
        const filmsList = useFilmsListStore();
        const filmsAccount = useFilmsAccountStore();
        
        const wrapper = mount(Account, {
            props: {
                errors: null,
                films: films_10_user,
                user: {
                    id: 77,
                    is_admin: true,
                    login: 'TestLogin'
                }
            },
            global: {
                mocks: {
                    $page: {
                        component: 'Auth/Account'
                    }
                },
                provide: { app, filmsList, filmsAccount }
            }
        });
        
        
        const table = wrapper.get('table.container');
        const tbody = table.get('tbody');
        const tbodyTr = tbody.findAll('tr');
        
        // Находим кнопку для удаления фильма
        const td3 = tbodyTr[3].findAll('td');
        const trashSvg = td3[5].findComponent(TrashSvg);
        // Модальное окно для удаления фильма скрыто
        expect(wrapper.vm.isShowFilmRemoveModal).toBe(false);
        expect(wrapper.findComponent(FilmRemoveModal).exists()).toBe(false);
        
        // Клик по кнопке открывает модальное окно
        await trashSvg.trigger('click');
        expect(wrapper.vm.isShowFilmRemoveModal).toBe(true);
        expect(wrapper.findComponent(FilmRemoveModal).exists()).toBe(true);
    });
});
