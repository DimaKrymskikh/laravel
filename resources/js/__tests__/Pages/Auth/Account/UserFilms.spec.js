import '@/bootstrap';
import { mount } from "@vue/test-utils";
import { router } from '@inertiajs/vue3';

import { setActivePinia, createPinia } from 'pinia';
import UserFilms from "@/Pages/Auth/Account/UserFilms.vue";
import AccountLayout from '@/Layouts/Auth/AccountLayout.vue';

import Dropdown from '@/Components/Elements/Dropdown.vue';
import Buttons from '@/Components/Pagination/Buttons.vue';
import Info from '@/Components/Pagination/Info.vue';
import EyeSvg from '@/Components/Svg/EyeSvg.vue';
import TrashSvg from '@/Components/Svg/TrashSvg.vue';
import FilmRemoveModal from '@/Components/Modal/Request/FilmRemoveModal.vue';
import EchoAuth from '@/Components/Broadcast/EchoAuth.vue';
import { useFilmsAccountStore } from '@/Stores/films';

import { films_10_user } from '@/__tests__/data/films';
import { AuthAccountLayoutStub } from '@/__tests__/stubs/layout';

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
        
const user = {
            id: 77,
            is_admin: false,
            login: 'TestLogin'
        };

const getWrapper = function(filmsAccount) {
    return mount(UserFilms, {
            props: {
                errors: null,
                films: films_10_user,
                user
            },
            global: {
                stubs: {
                    AccountLayout: AuthAccountLayoutStub,
                    FilmRemoveModal: true
                },
                provide: { filmsAccount }
            }
        });
};

describe("@/Pages/Auth/Account/UserFilms.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    
    it("Отрисовка UserFilms", () => {
        const filmsAccount = useFilmsAccountStore();
        
        const wrapper = getWrapper(filmsAccount);
        
        // Проверяем, что текущая страница пагинации сохранена в filmsAccount
        expect(wrapper.vm.filmsAccount.page).toBe(films_10_user.current_page);
        
        const accountLayout = wrapper.getComponent(AccountLayout);
        expect(accountLayout.props('user')).toStrictEqual(user);
        expect(accountLayout.props('errors')).toStrictEqual(null);
        expect(accountLayout.props('linksList')).toStrictEqual(wrapper.vm.linksList);
        
        // Отрисовывается кнопка изменения числа фильмов
        const dropdown = wrapper.getComponent(Dropdown);
        expect(dropdown.props('buttonName')).toBe('Число фильмов на странице');
        expect(dropdown.props('itemsNumberOnPage')).toBe(films_10_user.per_page);
        expect(dropdown.props('changeNumber')).toBe(wrapper.vm.changeNumberOfFilmsOnPage);
        
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
        const a4 = td3[4].get('a');
        expect(a4.attributes('href')).toBe(`/userfilms/${films_10_user.data[3].id}`);
        expect(a4.findComponent(EyeSvg).props('title')).toBe('Посмотреть карточку фильма');
        expect(td3[5].findComponent(TrashSvg).props('title')).toBe('Удалить фильм из своей коллекции');
        
        // Отрисовываются кнопки пагинации
        const buttons = wrapper.findComponent(Buttons);
        expect(buttons.exists()).toBe(true);
        
        const echoAuth = wrapper.getComponent(EchoAuth);
        expect(echoAuth.props('user')).toStrictEqual(user);
        expect(echoAuth.props('events')).toStrictEqual(['RemoveFilmFromUserList']);
    });
    
    it("Показать модальное окно удаления фильма", async () => {
        const filmsAccount = useFilmsAccountStore();
        
        const wrapper = getWrapper(filmsAccount);
        
        const table = wrapper.get('table.container');
        const tbody = table.get('tbody');
        const tbodyTr = tbody.findAll('tr');
        
        // Находим кнопку для удаления фильма
        const td3 = tbodyTr[3].findAll('td');
        const trashSvg = td3[5].findComponent(TrashSvg);
        // Модальное окно для удаления фильма скрыто
        expect(wrapper.vm.isShowFilmRemoveModal).toBe(false);
        expect(wrapper.findComponent(FilmRemoveModal).exists()).toBe(false);
        
        // Клик по кнопке TrashSvg открывает модальное окно
        await trashSvg.trigger('click');
        expect(wrapper.vm.isShowFilmRemoveModal).toBe(true);
        const filmRemoveModal = wrapper.findComponent(FilmRemoveModal);
        expect(filmRemoveModal.exists()).toBe(true);
    });
    
    // Закрытие модального окна (вызов функции hideFilmRemoveModal) проверяется в FilmRemoveModal
    it("Функция hideFilmRemoveModal изменяет isShowFilmRemoveModal с true на false", () => {
        const filmsAccount = useFilmsAccountStore();
        
        const wrapper = getWrapper(filmsAccount);
        
        wrapper.vm.isShowFilmRemoveModal = true;
        wrapper.vm.hideFilmRemoveModal();
        expect(wrapper.vm.isShowFilmRemoveModal).toBe(false);
    });
    
    // В Dropdown проверяется клик по элементам DOM
    it("Функция changeNumberOfFilmsOnPage отправляет запрос на изменение числа фильмов на странице", () => {
        const filmsAccount = useFilmsAccountStore();
        
        const wrapper = getWrapper(filmsAccount);
        
        // Запрос не отправлен
        expect(router.get).not.toHaveBeenCalled();
        
        wrapper.vm.changeNumberOfFilmsOnPage(50);
        // Функция changeNumberOfFilmsOnPage отправила запрос с нужными параметрами
        expect(wrapper.vm.filmsAccount.page).toBe(1);
        expect(wrapper.vm.filmsAccount.perPage).toBe(50);
        expect(router.get).toHaveBeenCalledWith(wrapper.vm.filmsAccount.getUrl('/userfilms'));
    });
    
    it("Проверка работы фильтров", async () => {
        const filmsAccount = useFilmsAccountStore();
        
        const wrapper = getWrapper(filmsAccount);
        
        // Запрос не отправлен
        expect(router.get).not.toHaveBeenCalled();
        
        // При загрузке страницы изменяется filmsAccount.page (films_10_user.current_page == 5)
        expect(wrapper.vm.filmsAccount.page).toBe(5);
        // Остальные свойства дефолтные
        expect(wrapper.vm.filmsAccount.perPage).toBe(20);
        expect(wrapper.vm.filmsAccount.title).toBe('');
        expect(wrapper.vm.filmsAccount.description).toBe('');
        
        const thead = wrapper.get('thead');
        const inputs = thead.findAll('input');
        expect(inputs.length).toBe(2);
        
        await inputs[0].setValue('abc');
        expect(wrapper.vm.filmsAccount.title).toBe('abc');
        
        await inputs[1].setValue('12345');
        expect(wrapper.vm.filmsAccount.description).toBe('12345');
        
        await inputs[0].trigger('keyup', {key: 'a'});
        // Запрос не отправлен
        expect(router.get).not.toHaveBeenCalled();
        
        await inputs[0].trigger('keyup', {key: 'enter'});
        // Активная страница становится первой, и отправляется запрос с нужными параметрами
        expect(wrapper.vm.filmsAccount.page).toBe(1);
        expect(router.get).toHaveBeenCalledWith(wrapper.vm.filmsAccount.getUrl('/userfilms'));
    });
});
