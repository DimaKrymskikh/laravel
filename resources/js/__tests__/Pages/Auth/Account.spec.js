import { mount } from "@vue/test-utils";
import { router } from '@inertiajs/vue3';

import '@/bootstrap';

import { setActivePinia, createPinia } from 'pinia';
import Account from "@/Pages/Auth/Account.vue";
import PersonalData from '@/Pages/Auth/Account/PersonalData.vue';
import BreadCrumb from '@/Components/Elements/BreadCrumb.vue';
import Dropdown from '@/Components/Elements/Dropdown.vue';
import Buttons from '@/Components/Pagination/Buttons.vue';
import DangerButton from '@/Components/Buttons/Variants/DangerButton.vue';
import PrimaryButton from '@/Components/Buttons/Variants/PrimaryButton.vue';
import Info from '@/Components/Pagination/Info.vue';
import Bars3 from '@/Components/Svg/Bars3.vue';
import EyeSvg from '@/Components/Svg/EyeSvg.vue';
import CheckSvg from '@/Components/Svg/CheckSvg.vue';
import TrashSvg from '@/Components/Svg/TrashSvg.vue';
import AccountRemoveModal from '@/Components/Modal/Request/AccountRemoveModal.vue';
import AdminModal from '@/Components/Modal/Request/AdminModal.vue';
import FilmRemoveModal from '@/Components/Modal/Request/FilmRemoveModal.vue';
import { filmsCatalogStore, filmsAccountStore } from '@/Stores/films';

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
        const filmsCatalog = filmsCatalogStore();
        const filmsAccount = filmsAccountStore();
        
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
                provide: { filmsCatalog, filmsAccount }
            }
        });
        
        // Проверяем, что текущая страница пагинации сохранена в filmsCatalog
        expect(wrapper.vm.filmsAccount.page).toBe(films_10_user.current_page);
        
        // Отрисовывается кнопка изменения числа фильмов
        const dropdown = wrapper.getComponent(Dropdown);
        expect(dropdown.text()).toBe('Число фильмов на странице');
        
        // Отрисовывается кнопка удаления аккаунта
        const dangerButton = wrapper.getComponent(DangerButton);
        expect(dangerButton.text()).toBe('Удалить аккаунт');
        
        // Отрисовывается кнопка манипуляции с администрированием (is_admin: false)
        const primaryButton = wrapper.getComponent(PrimaryButton);
        expect(primaryButton.text()).toBe('Сделать себя админом');
        
        // Имеется кнопка для открытия/скрытия личных данных
        expect(wrapper.getComponent(Bars3).attributes('title')).toBe('Личные данные');
        // Персональные данные скрыты
        expect(wrapper.vm.isPersonalData).toBe(false);
        expect(wrapper.findComponent(PersonalData).exists()).toBe(false);
        
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
    
    it("Показать/скрыть личные данные", async () => {
        const filmsCatalog = filmsCatalogStore();
        const filmsAccount = filmsAccountStore();
        
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
                provide: { filmsCatalog, filmsAccount },
                stubs: { FormButton: true }
            }
        });
        
        // Имеется кнопка для открытия/скрытия личных данных
        const bar3 = wrapper.getComponent(Bars3);
        expect(bar3.attributes('title')).toBe('Личные данные');
        // Персональные данные скрыты
        expect(wrapper.vm.isPersonalData).toBe(false);
        expect(wrapper.findComponent(PersonalData).exists()).toBe(false);
        
        // Клик по кнопке открывает личные данные
        await bar3.trigger('click');
        expect(wrapper.vm.isPersonalData).toBe(true);
        expect(wrapper.findComponent(PersonalData).exists()).toBe(true);
        
        // Повторный клик по кнопке скрывает личные данные
        await bar3.trigger('click');
        expect(wrapper.vm.isPersonalData).toBe(false);
        expect(wrapper.findComponent(PersonalData).exists()).toBe(false);
    });
    
    it("Показать/скрыть модальное окно удаления аккаунта", async () => {
        const filmsCatalog = filmsCatalogStore();
        const filmsAccount = filmsAccountStore();
        
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
                provide: { filmsCatalog, filmsAccount }
            }
        });
        
        // Имеется кнопка 'Удалить аккаунт'
        const dangerButton = wrapper.getComponent(DangerButton);
        expect(dangerButton.text()).toBe('Удалить аккаунт');
        // Модальное окно для удаления аккаунта скрыто
        expect(wrapper.vm.isShowAccountRemoveModal).toBe(false);
        expect(wrapper.findComponent(AccountRemoveModal).exists()).toBe(false);
        
        // Клик по кнопке открывает модальное окно
        await dangerButton.trigger('click');
        expect(wrapper.vm.isShowAccountRemoveModal).toBe(true);
        expect(wrapper.findComponent(AccountRemoveModal).exists()).toBe(true);
    });
    
    it("Показать/скрыть модальное окно администрирования (is_admin: false)", async () => {
        const filmsCatalog = filmsCatalogStore();
        const filmsAccount = filmsAccountStore();
        
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
                provide: { filmsCatalog, filmsAccount }
            }
        });
        
        // Имеется кнопка манипуляции с администрированием (is_admin: false)
        const primaryButton = wrapper.getComponent(PrimaryButton);
        expect(primaryButton.text()).toBe('Сделать себя админом');
        // Модальное окно для манипуляции с администрированием скрыто
        expect(wrapper.vm.isShowAdminModal).toBe(false);
        expect(wrapper.findComponent(AdminModal).exists()).toBe(false);
        
        // Клик по кнопке открывает модальное окно
        await primaryButton.trigger('click');
        expect(wrapper.vm.isShowAdminModal).toBe(true);
        expect(wrapper.findComponent(AdminModal).exists()).toBe(true);
    });
    
    it("Показать/скрыть модальное окно администрирования (is_admin: true)", async () => {
        const filmsCatalog = filmsCatalogStore();
        const filmsAccount = filmsAccountStore();
        
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
                provide: { filmsCatalog, filmsAccount }
            }
        });
        
        // Имеется кнопка манипуляции с администрированием (is_admin: false)
        const primaryButton = wrapper.getComponent(PrimaryButton);
        expect(primaryButton.text()).toBe('Отказаться от администрирования');
        // Модальное окно для манипуляции с администрированием скрыто
        expect(wrapper.vm.isShowAdminModal).toBe(false);
        expect(wrapper.findComponent(AdminModal).exists()).toBe(false);
        
        // Клик по кнопке открывает модальное окно
        await primaryButton.trigger('click');
        expect(wrapper.vm.isShowAdminModal).toBe(true);
        expect(wrapper.findComponent(AdminModal).exists()).toBe(true);
    });
    
    it("Показать/скрыть модальное окно удаления фильма", async () => {
        const filmsCatalog = filmsCatalogStore();
        const filmsAccount = filmsAccountStore();
        
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
                provide: { filmsCatalog, filmsAccount }
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
