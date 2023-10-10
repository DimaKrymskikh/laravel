import { mount } from "@vue/test-utils";

import { setActivePinia, createPinia } from 'pinia';
import PrimaryButton from '@/Components/Buttons/Variants/PrimaryButton.vue';
import Cities from "@/Pages/Admin/Cities.vue";
import AddCityBlock from '@/Pages/Admin/Cities/AddCityBlock.vue';
import BreadCrumb from '@/Components/Elements/BreadCrumb.vue';
import AddCityModal from '@/Components/Modal/Request/Cities/AddCityModal.vue';
import RemoveCityModal from '@/Components/Modal/Request/Cities/RemoveCityModal.vue';
import UpdateCityModal from '@/Components/Modal/Request/Cities/UpdateCityModal.vue';
import UpdateTimeZoneModal from '@/Components/Modal/Request/UpdateTimeZoneModal.vue';
import PencilSvg from '@/Components/Svg/PencilSvg.vue';
import TrashSvg from '@/Components/Svg/TrashSvg.vue';
import { useAppStore } from '@/Stores/app';
import { useFilmsAccountStore } from '@/Stores/films';

import { cities } from '@/__tests__/data/cities';

// Делаем заглушку для Head
vi.mock('@inertiajs/vue3', async () => {
    const actual = await vi.importActual("@inertiajs/vue3");
    return {
        ...actual,
        Head: vi.fn()
    };
});

const getWrapper = function(app, filmsAccount, cities = []) {
    return mount(Cities, {
            props: {
                errors: {},
                cities
            },
            global: {
                mocks: {
                    $page: {
                        component: 'Admin/Cities'
                    }
                },
                provide: { app, filmsAccount }
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
    beforeEach(() => {
        setActivePinia(createPinia());
    });

    it("Отрисовка страницы 'Города' при наличии городов", async () => {
        const app = useAppStore();
        const filmsAccount = useFilmsAccountStore();
        
        const wrapper = getWrapper(app, filmsAccount, cities);
        
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
        
        // Находим элементы DOM, клик по которым открывает модальное окно для удаления города
        const removeCity = tbody.findAll('.remove-city');
        expect(removeCity.length).toBe(3);
        for(let i = 0; i < removeCity.length; i++) {
            expect(removeCity[i].getComponent(TrashSvg).exists()).toBe(true);
        }
        // Берём один элемент, который открывает модальное окно для удаления города
        const trashSvg = removeCity[1].getComponent(TrashSvg);
        // Модальное окно для удаления города отсутствует
        expect(wrapper.findComponent(RemoveCityModal).exists()).toBe(false);
        // Кликаем по иконке
        await trashSvg.trigger('click');
        const removeCityModal = wrapper.findComponent(RemoveCityModal);
        // Модальное окно для удаления города появляется, и содержит нужную информацию
        expect(removeCityModal.exists()).toBe(true);
        expect(removeCityModal.text()).toContain('Москва');
        expect(removeCityModal.text()).toContain('524901');
        
        // Находим элементы DOM, клик по которым открывает модальное окно для изменения города
        const updateCity = tbody.findAll('.update-city');
        expect(updateCity.length).toBe(3);
        for(let i = 0; i < updateCity.length; i++) {
            expect(updateCity[i].getComponent(PencilSvg).exists()).toBe(true);
        }
        // Берём один элемент, который открывает модальное окно для изменения города
        const pencilSvg = updateCity[2].getComponent(PencilSvg);
        // Модальное окно для изменения города отсутствует
        expect(wrapper.findComponent(UpdateCityModal).exists()).toBe(false);
        // Кликаем по иконке
        await pencilSvg.trigger('click');
        const updateCityModal = wrapper.findComponent(UpdateCityModal);
        // Модальное окно для изменения города появляется, и содержит нужную информацию
        expect(updateCityModal.exists()).toBe(true);
        expect(updateCityModal.find('input').element.value).toBe('Омск');
    });
    
    it("Отрисовка страницы 'Города' без городов", () => {
        const app = useAppStore();
        const filmsAccount = useFilmsAccountStore();
        
        const wrapper = getWrapper(app, filmsAccount);
        
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
    });
    
    it("Проверка модальных окон", async () => {
       const app = useAppStore();
        const filmsAccount = useFilmsAccountStore();
        
        const wrapper = getWrapper(app, filmsAccount, cities);
        
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
        const removeCityModal = wrapper.getComponent(RemoveCityModal);
        expect(removeCityModal.props('removeCity')).toBe(wrapper.vm.removeCity);
        expect(removeCityModal.props('hideRemoveCityModal')).toBe(wrapper.vm.hideRemoveCityModal);
        // Клик по кнопке 'Нет' закрывает модальное окно
        const modalNo = removeCityModal.get('#modal-no');
        await modalNo.trigger('click');
        expect(wrapper.findComponent(RemoveCityModal).exists()).toBe(false);
        
        // Проверяем модальное окно 'Изменение названия города'
        // В начальный момент модальное окно отсутствует
        expect(wrapper.findComponent(UpdateCityModal).exists()).toBe(false);
        const cityPencilSvg = activeTr.find('.update-city').getComponent(PencilSvg);
        await cityPencilSvg.trigger('click');
        // После клика появляется модальное окно
        const updateCityModal = wrapper.getComponent(UpdateCityModal);
        expect(updateCityModal.props('updateCity')).toBe(wrapper.vm.updateCity);
        expect(updateCityModal.props('hideUpdateCityModal')).toBe(wrapper.vm.hideUpdateCityModal);
        // Клик по кнопке 'Нет' закрывает модальное окно
        const cityModalNo = updateCityModal.get('#modal-no');
        await cityModalNo.trigger('click');
        expect(wrapper.findComponent(UpdateCityModal).exists()).toBe(false);
        
        // Проверяем модальное окно 'Изменение временного пояса'
        // В начальный момент модальное окно отсутствует
        expect(wrapper.findComponent(UpdateTimeZoneModal).exists()).toBe(false);
        const timezonePencilSvg = activeTr.find('.update-timezone').getComponent(PencilSvg);
        await timezonePencilSvg.trigger('click');
        // После клика появляется модальное окно
        const updateTimeZoneModal = wrapper.getComponent(UpdateTimeZoneModal);
        expect(updateTimeZoneModal.props('updateCity')).toBe(wrapper.vm.updateCity);
        expect(updateTimeZoneModal.props('hideUpdateTimeZoneModal')).toBe(wrapper.vm.hideUpdateTimeZoneModal);
        // Клик по кнопке 'Нет' закрывает модальное окно
        const timezoneModalNo = updateTimeZoneModal.get('#modal-no');
        await timezoneModalNo.trigger('click');
        expect(wrapper.findComponent(UpdateTimeZoneModal).exists()).toBe(false);
    });
});
