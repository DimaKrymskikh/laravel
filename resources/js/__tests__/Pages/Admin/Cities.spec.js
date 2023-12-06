import { mount } from "@vue/test-utils";

import PrimaryButton from '@/Components/Buttons/Variants/PrimaryButton.vue';
import Cities from "@/Pages/Admin/Cities.vue";
import AddCityBlock from '@/Components/Pages/Admin/Cities/AddCityBlock.vue';
import BreadCrumb from '@/Components/Elements/BreadCrumb.vue';
import AddCityModal from '@/Components/Modal/Request/Cities/AddCityModal.vue';
import RemoveCityModal from '@/Components/Modal/Request/Cities/RemoveCityModal.vue';
import UpdateCityModal from '@/Components/Modal/Request/Cities/UpdateCityModal.vue';
import UpdateTimeZoneModal from '@/Components/Modal/Request/UpdateTimeZoneModal.vue';
import PencilSvg from '@/Components/Svg/PencilSvg.vue';
import TrashSvg from '@/Components/Svg/TrashSvg.vue';

import { cities } from '@/__tests__/data/cities';
import { AdminLayoutStub } from '@/__tests__/stubs/layout';

// Делаем заглушку для Head
vi.mock('@inertiajs/vue3', async () => {
    const actual = await vi.importActual("@inertiajs/vue3");
    return {
        ...actual,
        Head: vi.fn()
    };
});

const getWrapper = function(cities = []) {
    return mount(Cities, {
            props: {
                errors: {},
                cities
            },
            global: {
                stubs: {
                    AdminLayout: AdminLayoutStub,
                    RemoveCityModal: true,
                    UpdateCityModal: true,
                    UpdateTimeZoneModal: true
                }
            }
        });
};
    
const checkH1 = function(wrapper) {
    const h1 = wrapper.get('h1');
    expect(h1.text()).toBe('Города');
};

const checkBreadCrumb = function(wrapper) {
    const breadCrumb = wrapper.getComponent(BreadCrumb);
    expect(breadCrumb.isVisible()).toBe(true);
    const li = breadCrumb.findAll('li');
    expect(li.length).toBe(2);
    expect(li[0].find('a[href="/admin"]').exists()).toBe(true);
    expect(li[0].text()).toBe('Страница админа');
    expect(li[1].find('a').exists()).toBe(false);
    expect(li[1].text()).toBe('Города');
};

describe("@/Pages/Admin/Cities.vue", () => {
    it("Отрисовка страницы 'Города' при наличии городов", async () => {
        const wrapper = getWrapper(cities);
        
        // Проверяем заголовок
        checkH1(wrapper);
        
        // Проверяем хлебные крошки
        checkBreadCrumb(wrapper);
        
        // На странице присутствует компонента AddCityBlock
        const addCityBlock = wrapper.getComponent(AddCityBlock);
        expect(addCityBlock.isVisible()).toBe(true);
        
        // Проверяем таблицу городов
        const table = wrapper.get('table');
        expect(table.isVisible()).toBe(true);
        // На странице отсутствует запись
        expect(wrapper.text()).not.toContain('Ещё ни один город не добавлен');
        
        // Проверяем заголовок таблицы городов
        const thead = table.get('thead');
        expect(thead.isVisible()).toBe(true);
        const th = thead.findAll('th');
        expect(th.length).toBe(7);
        expect(th[0].text()).toBe('#');
        expect(th[1].text()).toBe('Город');
        expect(th[2].text()).toBe('');
        expect(th[3].text()).toBe('Временной пояс');
        expect(th[4].text()).toBe('');
        expect(th[5].text()).toBe('OpenWeather Id');
        expect(th[6].text()).toBe('');
        
        // Проверяем тело таблицы городов
        const tbody = table.get('tbody');
        expect(tbody.isVisible()).toBe(true);
        const tr = tbody.findAll('tr');
        expect(tr.length).toBe(3);
        expect(tr[0].text()).toContain('Новосибирск');
        expect(tr[0].text()).toContain('1496747');
        expect(tr[1].text()).toContain('Москва');
        expect(tr[1].text()).toContain('524901');
        expect(tr[2].text()).toContain('Омск');
        expect(tr[2].text()).toContain('1496153');
        
        const tds = tr[1].findAll('td');
        expect(tds.length).toBe(7);
        expect(tds[0].text()).toBe('2');
        expect(tds[1].text()).toBe('Москва');
        expect(tds[2].getComponent(PencilSvg).props('title')).toBe('Редактировать название фильма');
        expect(tds[3].text()).toBe('Europe/Moscow');
        expect(tds[4].getComponent(PencilSvg).props('title')).toBe('Редактировать временной пояс');
        expect(tds[5].text()).toBe('524901');
        expect(tds[6].getComponent(TrashSvg).props('title')).toBe('Удалить город');
    });
    
    it("Отрисовка страницы 'Города' без городов", () => {
        const wrapper = getWrapper();
        
        // Проверяем заголовок
        checkH1(wrapper);
        
        // Проверяем хлебные крошки
        checkBreadCrumb(wrapper);
        
        // На странице присутствует компонента AddCityBlock
        const addCityBlock = wrapper.getComponent(AddCityBlock);
        expect(addCityBlock.isVisible()).toBe(true);
        
        // Таблица городов отсутствует
        const table = wrapper.find('table');
        expect(table.exists()).toBe(false);
        // На странице присутствует запись
        expect(wrapper.text()).toContain('Ещё ни один город не добавлен');
        
        // Модальное окно для удаления города отсутствует
        expect(wrapper.findComponent(RemoveCityModal).exists()).toBe(false);
        // Нет элементов DOM, клик по которым мог бы открыть модальное окно для удаления города
        const removeCity = wrapper.findAll('.remove-city');
        expect(removeCity.length).toBe(0);
        
        // Модальное окно для изменения города отсутствует
        expect(wrapper.findComponent(UpdateCityModal).exists()).toBe(false);
        // Нет элементов DOM, клик по которым мог бы открыть модальное окно для изменения города
        const updateCity = wrapper.findAll('.update-city');
        expect(updateCity.length).toBe(0);
        
        // Модальное окно для изменения временного пояса отсутствует
        expect(wrapper.findComponent(UpdateTimeZoneModal).exists()).toBe(false);
        // Нет элементов DOM, клик по которым мог бы открыть модальное окно для изменения временного пояса
        const updateTimezone = wrapper.findAll('.update-timezone');
        expect(updateTimezone.length).toBe(0);
    });
    
    it("Проверка появления модальных окон", async () => {
        const wrapper = getWrapper(cities);
        
        // Находим таблицу городов
        const table = wrapper.get('table');
        expect(table.isVisible()).toBe(true);
        
        // В теле таблицы городов берём одну строку
        const tbody = table.get('tbody');
        expect(tbody.isVisible()).toBe(true);
        const tr = tbody.findAll('tr');
        expect(tr.length).toBe(3);
        const activeTr = tr[1];
        expect(activeTr.text()).toContain('Москва');
        expect(activeTr.text()).toContain('524901');
        
        // Проверяем модальное окно 'Удаления города'
        // В начальный момент модальное окно отсутствует
        expect(wrapper.findComponent(RemoveCityModal).exists()).toBe(false);
        const trashSvg = activeTr.getComponent(TrashSvg);
        await trashSvg.trigger('click');
        // После клика появляется модальное окно
        expect(wrapper.findComponent(RemoveCityModal).exists()).toBe(true);
        
        // Проверяем модальное окно 'Изменение названия города'
        // В начальный момент модальное окно отсутствует
        expect(wrapper.findComponent(UpdateCityModal).exists()).toBe(false);
        const cityPencilSvg = activeTr.find('.update-city').getComponent(PencilSvg);
        await cityPencilSvg.trigger('click');
        // После клика появляется модальное окно
        expect(wrapper.findComponent(UpdateCityModal).exists()).toBe(true);
        
        // Проверяем модальное окно 'Изменение временного пояса'
        // В начальный момент модальное окно отсутствует
        expect(wrapper.findComponent(UpdateTimeZoneModal).exists()).toBe(false);
        const timezonePencilSvg = activeTr.find('.update-timezone').getComponent(PencilSvg);
        await timezonePencilSvg.trigger('click');
        // После клика появляется модальное окно
        expect(wrapper.findComponent(UpdateTimeZoneModal).exists()).toBe(true);
    });
    
    it("Функция hideRemoveCityModal изменяет isShowRemoveCityModal с true на false", () => {
        const wrapper = getWrapper();
        
        wrapper.vm.isShowRemoveCityModal = true;
        wrapper.vm.hideRemoveCityModal();
        expect(wrapper.vm.isShowRemoveCityModal).toBe(false);
    });
    
    it("Функция hideUpdateCityModal изменяет isShowUpdateCityModal с true на false", () => {
        const wrapper = getWrapper();
        
        wrapper.vm.isShowUpdateCityModal = true;
        wrapper.vm.hideUpdateCityModal();
        expect(wrapper.vm.isShowUpdateCityModal).toBe(false);
    });
    
    it("Функция hideUpdateTimeZoneModal изменяет isShowUpdateTimeZoneModal с true на false", () => {
        const wrapper = getWrapper();
        
        wrapper.vm.isShowUpdateTimeZoneModal = true;
        wrapper.vm.hideUpdateTimeZoneModal();
        expect(wrapper.vm.isShowUpdateTimeZoneModal).toBe(false);
    });
});
