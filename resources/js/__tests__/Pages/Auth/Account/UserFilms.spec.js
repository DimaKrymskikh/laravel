import '@/bootstrap';
import { mount } from "@vue/test-utils";
import { router } from '@inertiajs/vue3';

import { setActivePinia, createPinia } from 'pinia';
import UserFilms from "@/Pages/Auth/Account/UserFilms.vue";
import AccountLayout from '@/Layouts/Auth/AccountLayout.vue';

import AlertPrimary from '@/Components/Alerts/AlertPrimary.vue';
import Dropdown from '@/Components/Elements/Dropdown.vue';
import Buttons from '@/Components/Pagination/Buttons.vue';
import Info from '@/Components/Pagination/Info.vue';
import EyeSvg from '@/Components/Svg/EyeSvg.vue';
import TrashSvg from '@/Components/Svg/TrashSvg.vue';
import FilmRemoveModal from '@/Components/Modal/Request/FilmRemoveModal.vue';
import EchoAuth from '@/Components/Broadcast/EchoAuth.vue';
import { useFilmsAccountStore } from '@/Stores/films';
import { useLanguagesListStore } from '@/Stores/languages';

import { films_10_user, films_0 } from '@/__tests__/data/films';
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

const getWrapper = function(films) {
    return mount(UserFilms, {
            props: {
                errors: null,
                films,
                user
            },
            global: {
                stubs: {
                    AccountLayout: AuthAccountLayoutStub,
                    FilmRemoveModal: true
                },
                provide: {
                    filmsAccount: useFilmsAccountStore(),
                    languagesList: useLanguagesListStore()
                }
            }
        });
};

const checkAccountLayout = function(wrapper) {
    const accountLayout = wrapper.getComponent(AccountLayout);
    expect(accountLayout.props('user')).toStrictEqual(user);
    expect(accountLayout.props('errors')).toStrictEqual(null);
    expect(accountLayout.props('linksList')).toStrictEqual(wrapper.vm.linksList);
};

const checkDropdown = function(wrapper, films) {
    const dropdown = wrapper.getComponent(Dropdown);
    expect(dropdown.props('buttonName')).toBe('Число фильмов на странице');
    expect(dropdown.props('itemsNumberOnPage')).toBe(films.per_page);
    expect(dropdown.props('changeNumber')).toBe(wrapper.vm.changeNumberOfFilmsOnPage);
};

const checkTableHeaders = function(tag) {
    const th = tag.findAll('th');
    expect(th.length).toBe(7);
    expect(th[0].text()).toBe('#');
    expect(th[1].text()).toBe('Название');
    expect(th[2].text()).toBe('Описание');
    expect(th[3].text()).toBe('Язык');
    expect(th[4].text()).toBe('Год выхода');
    expect(th[5].text()).toBe('');
    expect(th[6].text()).toBe('');
};

const checkTableInputFields = function(tag) {
    const th = tag.findAll('th');
    expect(th.length).toBe(7);
    expect(th[0].text()).toBe('');
    expect(th[1].get('input').element.value).toBe('');
    expect(th[2].get('input').element.value).toBe('');
    expect(th[3].text()).toBe('все');
    expect(th[4].text()).toBe('все');
    expect(th[5].text()).toBe('');
    expect(th[6].text()).toBe('');
};

const checkEchoAuth = function(wrapper) {
    const echoAuth = wrapper.getComponent(EchoAuth);
    expect(echoAuth.props('user')).toStrictEqual(user);
    expect(echoAuth.props('events')).toStrictEqual(['RemoveFilmFromUserList']);
};

describe("@/Pages/Auth/Account/UserFilms.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    
    it("Отрисовка UserFilms", () => {
        const wrapper = getWrapper(films_10_user);
        
        checkAccountLayout(wrapper);
        
        // Проверяем, что текущая страница пагинации сохранена в filmsAccount
        expect(wrapper.vm.filmsAccount.page).toBe(films_10_user.current_page);
        
        // Отрисовывается кнопка изменения числа фильмов
        checkDropdown(wrapper, films_10_user);
        
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
        checkTableHeaders(theadTr[0]);
        
        // Второй ряд содержит поля ввода
        checkTableInputFields(theadTr[1]);
        
        // Тело таблицы состоит из рядов с данными фильмов
        const tbody = table.get('tbody');
        expect(tbody.isVisible()).toBe(true);
        const tbodyTr = tbody.findAll('tr');
        expect(tbodyTr.length).toBe(films_10_user.per_page);
        
        // Отрисовка одного ряда тела таблицы
        const td3 = tbodyTr[3].findAll('td');
        expect(td3.length).toBe(7);
        expect(td3[0].text()).toBe(`${films_10_user.from + 3}`);
        expect(td3[1].text()).toBe(films_10_user.data[3].title);
        expect(td3[2].text()).toBe(films_10_user.data[3].description);
        expect(td3[3].text()).toBe(films_10_user.data[3].languageName);
        expect(td3[4].text()).toBe(films_10_user.data[3].releaseYear);
        const a5 = td3[5].get('a');
        expect(a5.attributes('href')).toBe(`/userfilms/${films_10_user.data[3].id}`);
        expect(a5.findComponent(EyeSvg).props('title')).toBe('Посмотреть карточку фильма');
        expect(td3[6].findComponent(TrashSvg).props('title')).toBe('Удалить фильм из своей коллекции');
        
        // Отрисовываются кнопки пагинации
        const buttons = wrapper.findComponent(Buttons);
        expect(buttons.exists()).toBe(true);
        
        expect(wrapper.findComponent(AlertPrimary).exists()).toBe(false);
        
        checkEchoAuth(wrapper);
    });
    
    it("Отрисовка UserFilms без фильмов", () => {
        const wrapper = getWrapper(films_0);
        
        checkAccountLayout(wrapper);
        
        // Проверяем, что текущая страница пагинации сохранена в filmsAccount
        expect(wrapper.vm.filmsAccount.page).toBe(films_0.current_page);
        
        // Отрисовывается кнопка изменения числа фильмов
        checkDropdown(wrapper, films_0);
        
        // Отрисовывается таблица фильмов
        const table = wrapper.get('table.container');
        expect(table.isVisible()).toBe(true);
        
        // Отсутствует заголовок к таблице
        const info = table.get('caption').findComponent(Info);
        expect(info.exists()).toBe(false);
        
        // В шапке таблицы два ряда
        const thead = table.get('thead');
        expect(thead.isVisible()).toBe(true);
        const theadTr = thead.findAll('tr');
        expect(theadTr.length).toBe(2);
        
        // Первый ряд содержит заголовки
        checkTableHeaders(theadTr[0]);
        
        // Второй ряд содержит поля ввода
        checkTableInputFields(theadTr[1]);
        
        // В теле таблицы нет рядов с фильмами
        const tbody = table.get('tbody');
        expect(tbody.isVisible()).toBe(true);
        const tbodyTr = tbody.findAll('tr');
        expect(tbodyTr.length).toBe(0);
        
        // Отсутствуют кнопки пагинации
        const buttons = wrapper.findComponent(Buttons);
        expect(buttons.exists()).toBe(false);
        
        expect(wrapper.findComponent(AlertPrimary).exists()).toBe(true);
        
        checkEchoAuth(wrapper);
    });
    
    it("Показать модальное окно удаления фильма", async () => {
        const wrapper = getWrapper(films_10_user);
        
        const table = wrapper.get('table.container');
        const tbody = table.get('tbody');
        const tbodyTr = tbody.findAll('tr');
        
        // Находим кнопку для удаления фильма
        const td3 = tbodyTr[3].findAll('td');
        const trashSvg = td3[6].findComponent(TrashSvg);
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
        const wrapper = getWrapper(films_10_user);
        
        wrapper.vm.isShowFilmRemoveModal = true;
        wrapper.vm.hideFilmRemoveModal();
        expect(wrapper.vm.isShowFilmRemoveModal).toBe(false);
    });
    
    // В Dropdown проверяется клик по элементам DOM
    it("Функция changeNumberOfFilmsOnPage отправляет запрос на изменение числа фильмов на странице", () => {
        const wrapper = getWrapper(films_10_user);
        
        // Запрос не отправлен
        expect(router.get).not.toHaveBeenCalled();
        
        wrapper.vm.changeNumberOfFilmsOnPage(50);
        // Функция changeNumberOfFilmsOnPage отправила запрос с нужными параметрами
        expect(wrapper.vm.filmsAccount.page).toBe(1);
        expect(wrapper.vm.filmsAccount.perPage).toBe(50);
        expect(router.get).toHaveBeenCalledWith(wrapper.vm.filmsAccount.getUrl('/userfilms'));
    });
    
    it("Проверка работы фильтров", async () => {
        vi.useFakeTimers();
        
        const wrapper = getWrapper(films_10_user);
        
        // Запрос не отправлен
        expect(router.get).not.toHaveBeenCalled();
        
        // При загрузке страницы изменяется filmsAccount.page (films_10_user.current_page == 5)
        expect(wrapper.vm.filmsAccount.page).toBe(films_10_user.current_page);
        // Число фильмов на странице films_10_user.per_page = 10
        expect(wrapper.vm.filmsAccount.perPage).toBe(films_10_user.per_page);
        // Остальные свойства дефолтные
        expect(wrapper.vm.filmsAccount.title).toBe('');
        expect(wrapper.vm.filmsAccount.description).toBe('');
        
        const thead = wrapper.get('thead');
        const inputs = thead.findAll('input');
        expect(inputs.length).toBe(2);
        
        await inputs[0].setValue('abc');
        expect(wrapper.vm.filmsAccount.title).toBe('abc');
        
        await inputs[1].setValue('12345');
        expect(wrapper.vm.filmsAccount.description).toBe('12345');
        
        // Нажимаем три клавиши, запрос отправляется один раз
        expect(router.get).not.toHaveBeenCalled();
        await inputs[0].trigger('keyup', {key: 'a'});
        await inputs[0].trigger('keyup', {key: 'b'});
        await inputs[0].trigger('keyup', {key: 'c'});
        vi.advanceTimersByTime(2000);
         // Активная страница становится первой, и отправляется запрос с нужными параметрами
        expect(wrapper.vm.filmsAccount.page).toBe(1);
        expect(router.get).toHaveBeenCalledWith(wrapper.vm.filmsAccount.getUrl('/userfilms'));
    });
    
    it("Функция setNewLanguageName изменяет languageName", async () => {
        const wrapper = getWrapper(films_10_user);
        
        expect(wrapper.vm.languageName).toBe('');
        wrapper.vm.setNewLanguageName('Новый язык');
        expect(wrapper.vm.languageName).toBe('Новый язык');
    });
    
    it("Функция setNewReleaseYear изменяет releaseYear", async () => {
        const wrapper = getWrapper(films_10_user);
        
        expect(wrapper.vm.releaseYear).toBe('');
        wrapper.vm.setNewReleaseYear('1970');
        expect(wrapper.vm.releaseYear).toBe('1970');
    });
});
