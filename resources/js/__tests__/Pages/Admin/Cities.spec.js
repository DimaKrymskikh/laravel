import { mount } from "@vue/test-utils";

import { setActivePinia, createPinia } from 'pinia';
import Cities from "@/Pages/Admin/Cities.vue";
import BreadCrumb from '@/Components/Elements/BreadCrumb.vue';
import AddCityModal from '@/Components/Modal/Request/AddCityModal.vue';
import RemoveCityModal from '@/Components/Modal/Request/RemoveCityModal.vue';
import UpdateCityModal from '@/Components/Modal/Request/UpdateCityModal.vue';
import PencilSvg from '@/Components/Svg/PencilSvg.vue';
import TrashSvg from '@/Components/Svg/TrashSvg.vue';
import { filmsAccountStore } from '@/Stores/films';

import { cities } from '@/__tests__/data/cities';

// Делаем заглушку для Head
vi.mock('@inertiajs/vue3', async () => {
    const actual = await vi.importActual("@inertiajs/vue3");
    return {
        ...actual,
        Head: vi.fn()
    };
});

describe("@/Pages/Admin/Cities.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    
    it("Отрисовка страницы 'Города' при наличии городов", async () => {
        const filmsAccount = filmsAccountStore();
        
        const wrapper = mount(Cities, {
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
                provide: { filmsAccount }
            }
        });
        
        // Проверяем заголовок
        checkH1(wrapper);
        
        // Проверяем хлебные крошки
        checkBreadCrumb(wrapper);
        
        // Проверяем появление модального окна для добавления города
        checkAddCity(wrapper);
        
        // Проверяем таблицу городов
        const table = wrapper.get('table');
        expect(table.isVisible()).toBe(true);
        // На странице отсутствует запись
        expect(wrapper.text()).not.toContain('Ещё ни один город не добавлен');
        
        // Проверяем заголовок таблицы городов
        const thead = table.get('thead');
        expect(thead.isVisible()).toBe(true);
        const th = thead.findAll('th');
        expect(th.length).toBe(5);
        expect(th[0].text()).toBe('#');
        expect(th[1].text()).toBe('Город');
        expect(th[2].text()).toBe('');
        expect(th[3].text()).toBe('OpenWeather Id');
        expect(th[4].text()).toBe('');
        
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
        const filmsAccount = filmsAccountStore();
        
        const wrapper = mount(Cities, {
            props: {
                errors: {},
                cities: []
            },
            global: {
                mocks: {
                    $page: {
                        component: 'Admin/Cities'
                    }
                },
                provide: { filmsAccount }
            }
        });
        
        // Проверяем заголовок
        checkH1(wrapper);
        
        // Проверяем хлебные крошки
        checkBreadCrumb(wrapper);
        
        // Проверяем появление модального окна для добавления города
        checkAddCity(wrapper);
        
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
    
    const checkH1 = function(wrapper) {
        const h1 = wrapper.get('h1');
        expect(h1.text()).toBe('Города');
    };
    
    const checkBreadCrumb = function(wrapper) {
        const breadCrumb = wrapper.findComponent(BreadCrumb);
        expect(breadCrumb.exists()).toBe(true);
        const li = breadCrumb.findAll('li');
        expect(li.length).toBe(2);
        expect(li[0].find('a[href="/admin"]').exists()).toBe(true);
        expect(li[0].text()).toBe('Страница админа');
        expect(li[1].find('a').exists()).toBe(false);
        expect(li[1].text()).toBe('Города');
    };
    
    const checkAddCity = async function(wrapper) {
        const addCity = wrapper.get('#add-city');
        expect(addCity.isVisible()).toBe(true);
        expect(wrapper.findComponent(AddCityModal).exists()).toBe(false);
        await addCity.trigger('click');
        expect(wrapper.findComponent(AddCityModal).exists()).toBe(true);
    };
});
