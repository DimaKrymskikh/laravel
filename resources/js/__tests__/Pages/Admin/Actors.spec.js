import { mount } from "@vue/test-utils";
import { router } from '@inertiajs/vue3';
import { setActivePinia, createPinia } from 'pinia';

import Actors from "@/Pages/Admin/Actors.vue";
import AddActorBlock from '@/Components/Pages/Admin/Actors/AddActorBlock.vue';
import RemoveActorModal from '@/Components/Modal/Request/Actors/RemoveActorModal.vue';
import UpdateActorModal from '@/Components/Modal/Request/Actors/UpdateActorModal.vue';
import Dropdown from '@/Components/Elements/Dropdown.vue';
import Buttons from '@/Components/Pagination/Buttons.vue';
import BreadCrumb from '@/Components/Elements/BreadCrumb.vue';
import PencilSvg from '@/Components/Svg/PencilSvg.vue';
import TrashSvg from '@/Components/Svg/TrashSvg.vue';
import { useActorsListStore } from '@/Stores/actors';

import { actors, actors_0 } from '@/__tests__/data/actors';
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

const getWrapper = function(actors, actorsList) {
    return mount(Actors, {
            props: {
                errors: {},
                actors
            },
            global: {
                stubs: {
                    AdminLayout: AdminLayoutStub,
                    RemoveActorModal: true,
                    UpdateActorModal: true
                },
                provide: { actorsList }
            }
        });
};

const recordWhenTheActorTableIsEmpty = 'Ни один актёр не найден, или ещё ни один актёр не добавлен.';
    
const checkH1 = function(wrapper) {
    const h1 = wrapper.get('h1');
    expect(h1.text()).toBe('Актёры');
};

const checkBreadCrumb = function(wrapper) {
    const breadCrumb = wrapper.getComponent(BreadCrumb);
    expect(breadCrumb.isVisible()).toBe(true);
    const li = breadCrumb.findAll('li');
    expect(li.length).toBe(2);
    expect(li[0].find('a[href="/admin"]').exists()).toBe(true);
    expect(li[0].text()).toBe('Страница админа');
    expect(li[1].find('a').exists()).toBe(false);
    expect(li[1].text()).toBe('Актёры');
};

const checkElements = function(wrapper) {
    const dropdown = wrapper.getComponent(Dropdown);
    expect(dropdown.props('buttonName')).toBe('Число актёров на странице');
    expect(dropdown.props('itemsNumberOnPage')).toBe(wrapper.vm.props.actors.per_page);
    expect(dropdown.props('changeNumber')).toBe(wrapper.vm.changeNumberOfActorsOnPage);
    expect(dropdown.props('options')).toStrictEqual([10, 20, 50, 100, 500]);

    expect(wrapper.getComponent(AddActorBlock).isVisible()).toBe(true);
};

describe("@/Pages/Admin/Actors.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    afterEach(async () => {
        await router.get.mockClear();
    });
    
    it("Отрисовка страницы 'Актёры' при наличии актёров", () => {
        const actorsList = useActorsListStore();
        
        const wrapper = getWrapper(actors, actorsList);
        
        // Проверяем заголовок
        checkH1(wrapper);
        
        // Проверяем хлебные крошки
        checkBreadCrumb(wrapper);
        
        checkElements(wrapper);
        
        // Проверяем таблицу актёров
        const table = wrapper.get('table');
        expect(table.isVisible()).toBe(true);
        // На странице отсутствует запись
        expect(wrapper.text()).not.toContain(recordWhenTheActorTableIsEmpty);
        
        const caption = table.get('caption');
        expect(caption.text()).toBe(`Показано ${actors.per_page} актёров с ${actors.from} по ${actors.to} из ${actors.total}`);
        
        // Проверяем заголовок таблицы городов
        const thead = table.get('thead');
        expect(thead.isVisible()).toBe(true);
        const trHead = thead.findAll('tr');
        expect(trHead.length).toBe(2);
        const th0 = trHead[0].findAll('th');
        expect(th0.length).toBe(4);
        expect(th0[0].text()).toBe('#');
        expect(th0[1].text()).toBe('Актёр');
        expect(th0[2].text()).toBe('');
        expect(th0[3].text()).toBe('');
        const th1 = trHead[1].findAll('th');
        expect(th1.length).toBe(4);
        expect(th1[1].text()).toBe('');
        expect(th1[1].get('input').element.value).toBe('');
        expect(th1[2].text()).toBe('');
        expect(th1[3].text()).toBe('');
        
        // Проверяем тело таблицы городов
        const tbody = table.get('tbody');
        expect(tbody.isVisible()).toBe(true);
        const tr = tbody.findAll('tr');
        expect(tr.length).toBe(3);
        
        const tds = tr[1].findAll('td');
        expect(tds.length).toBe(4);
        expect(tds[0].text()).toBe('2');
        expect(tds[1].text()).toBe('Nick Wahlberg');
        expect(tds[2].getComponent(PencilSvg).props('title')).toBe('Редактировать актёра');
        expect(tds[3].getComponent(TrashSvg).props('title')).toBe('Удалить актёра');
        
        const buttons = wrapper.getComponent(Buttons);
        expect(buttons.props('links')).toStrictEqual(actors.links);
    });
    
    it("Отрисовка страницы 'Актёры' без актёров", () => {
        const actorsList = useActorsListStore();
        
        const wrapper = getWrapper(actors_0, actorsList);
        
        // Проверяем заголовок
        checkH1(wrapper);
        
        // Проверяем хлебные крошки
        checkBreadCrumb(wrapper);
        
        checkElements(wrapper);
        
        // Таблица пустая
        const table = wrapper.get('table');
        // Нет подписи таблицы
        expect(table.find('caption').exists()).toBe(false);
        // Присутствует заголовок таблицы
        expect(table.find('thead').exists()).toBe(true);
        // В теле таблицы нет ни одной строки
        const tbody = table.get('tbody');
        const trs = tbody.findAll('tr');
        expect(trs.length).toBe(0);
        // На странице присутствует запись
        expect(wrapper.text()).toContain(recordWhenTheActorTableIsEmpty);
        
        expect(wrapper.findComponent(Buttons).exists()).toBe(false);
    });
    
    it("Проверка работы фильтров", async () => {
        const actorsList = useActorsListStore();
        // actors.per_page не равен дефолтному actorsList.perPage
        actorsList.perPage = actors.per_page;
        
        const wrapper = getWrapper(actors, actorsList);
        
        // Запрос не отправлен
        expect(router.get).not.toHaveBeenCalled();
        
        // Начальные данные
        expect(wrapper.vm.actorsList.page).toBe(2);
        expect(wrapper.vm.actorsList.perPage).toBe(10);
        expect(wrapper.vm.actorsList.name).toBe('');
        
        const input = wrapper.get('thead').get('input');
        
        await input.setValue('Имя');
        expect(wrapper.vm.actorsList.name).toBe('Имя');
        
        await input.trigger('keyup', {key: 'a'});
        // Запрос не отправлен
        expect(router.get).not.toHaveBeenCalled();
        
        await input.trigger('keyup', {key: 'enter'});
        // Активная страница становится первой, и отправляется запрос с нужными параметрами
        expect(wrapper.vm.actorsList.page).toBe(1);
        expect(router.get).toHaveBeenCalledWith(wrapper.vm.actorsList.getUrl());
    });
    
    it("Изменение числа актёров на странице", async () => {
        const actorsList = useActorsListStore();
        actorsList.perPage = actors.per_page;
        
        const wrapper = getWrapper(actors, actorsList);
        
        expect(wrapper.vm.actorsList.page).toBe(2);
        expect(wrapper.vm.actorsList.perPage).toBe(10);
        
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
        expect(li.length).toBe(5);
        expect(li[0].text()).toBe('10');
        expect(li[1].text()).toBe('20');
        expect(li[2].text()).toBe('50');
        expect(li[3].text()).toBe('100');
        expect(li[4].text()).toBe('500');
        
        // Запрос на сервер не отправлен
        expect(router.get).not.toHaveBeenCalled();
        // Кликаем по кнопке 50
        await li[2].trigger('click');
        // Отправляется запрос на сервер с правильным параметром
        expect(router.get).toHaveBeenCalledTimes(1);
        expect(router.get).toHaveBeenCalledWith(wrapper.vm.actorsList.getUrl());
        // В actorsList изменяются текущая страница и число фильмов на странице
        expect(wrapper.vm.actorsList.page).toBe(1);
        expect(wrapper.vm.actorsList.perPage).toBe(50);
    });
    
    it("Проверка появления модальных окон", async () => {
        const actorsList = useActorsListStore();
        
        const wrapper = getWrapper(actors, actorsList);
        
        // Находим таблицу
        const table = wrapper.get('table');
        expect(table.isVisible()).toBe(true);
        
        // В теле таблицы берём одну строку
        const tbody = table.get('tbody');
        expect(tbody.isVisible()).toBe(true);
        const tr = tbody.findAll('tr');
        expect(tr.length).toBe(3);
        const activeTr = tr[1];
        expect(activeTr.text()).toContain(`${actors.data[1].first_name} ${actors.data[1].last_name}`);
        
        // Проверяем модальное окно 'Удаления актёра'
        // В начальный момент модальное окно отсутствует
        expect(wrapper.findComponent(RemoveActorModal).exists()).toBe(false);
        const trashSvg = activeTr.getComponent(TrashSvg);
        await trashSvg.trigger('click');
        // После клика появляется модальное окно
        expect(wrapper.findComponent(RemoveActorModal).exists()).toBe(true);
        
        // Проверяем модальное окно 'Изменение полного имени актёра'
        // В начальный момент модальное окно отсутствует
        expect(wrapper.findComponent(UpdateActorModal).exists()).toBe(false);
        const cityPencilSvg = activeTr.find('.update-actor').getComponent(PencilSvg);
        await cityPencilSvg.trigger('click');
        // После клика появляется модальное окно
        expect(wrapper.findComponent(UpdateActorModal).exists()).toBe(true);
    });
    
    it("Функция hideRemoveActorModal изменяет isShowRemoveActorModal с true на false", () => {
        const actorsList = useActorsListStore();
        
        const wrapper = getWrapper(actors, actorsList);
        
        wrapper.vm.isShowRemoveActorModal = true;
        wrapper.vm.hideRemoveActorModal();
        expect(wrapper.vm.isShowRemoveActorModal).toBe(false);
    });
    
    it("Функция hideUpdateActorModal изменяет isShowUpdateActorModal с true на false", () => {
        const actorsList = useActorsListStore();
        
        const wrapper = getWrapper(actors, actorsList);
        
        wrapper.vm.isShowUpdateActorModal = true;
        wrapper.vm.hideUpdateActorModal();
        expect(wrapper.vm.isShowUpdateActorModal).toBe(false);
    });
});
