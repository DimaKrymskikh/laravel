import { mount } from "@vue/test-utils";

import Cities from "@/Pages/Guest/Cities.vue";
import GuestLayout from '@/Layouts/GuestLayout.vue';
import BreadCrumb from '@/Components/Elements/BreadCrumb.vue';

import { cities } from '@/__tests__/data/cities';
import { GuestLayoutStub } from '@/__tests__/stubs/layout';

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
                cities,
                errors: {}
            },
            global: {
                stubs: {
                    GuestLayout: GuestLayoutStub
                }
            }
        });
};

// Проверка названия страницы
const checkH1 = function(wrapper) {
    const h1 = wrapper.get('h1');
    expect(h1.text()).toBe('Города');
};

// Проверка хлебных крошек
const checkBreadCrumb = function(wrapper) {
        const breadCrumb = wrapper.findComponent(BreadCrumb);
        expect(breadCrumb.exists()).toBe(true);
        // Хлебные крошки состоят из двух элементов
        const li = breadCrumb.findAll('li');
        expect(li.length).toBe(2);
        // Ссылка на страницу 'Главная страница'
        const a0 = li[0].find('a');
        expect(a0.text()).toBe('Главная страница');
        expect(a0.attributes('href')).toBe('/guest');
        // Название текущей страницы
        expect(li[1].text()).toBe('Города');
        expect(li[1].find('a').exists()).toBe(false);
};

describe("@/Pages/Guest/Cities.vue", () => {
    it("Отрисовка таблицы городов (гостевой режим)", () => {
        const wrapper = getWrapper(cities);
        
        expect(wrapper.findComponent(GuestLayout).exists()).toBe(true);
        
        checkH1(wrapper);
        
        checkBreadCrumb(wrapper);
        
        // Проверяем таблицу городов
        const table = wrapper.get('table');
        expect(table.isVisible()).toBe(true);
        // На странице отсутствует запись
        expect(wrapper.text()).not.toContain('Ещё ни один город не добавлен');
        
        // Проверяем заголовок таблицы городов
        const thead = table.get('thead');
        expect(thead.isVisible()).toBe(true);
        const th = thead.findAll('th');
        expect(th.length).toBe(3);
        expect(th[0].text()).toBe('#');
        expect(th[1].text()).toBe('Город');
        expect(th[2].text()).toBe('Временной пояс');
        
        // Проверяем тело таблицы городов
        const tbody = table.get('tbody');
        expect(tbody.isVisible()).toBe(true);
        const tr = tbody.findAll('tr');
        expect(tr.length).toBe(3);
        expect(tr[0].text()).toContain('Новосибирск');
        expect(tr[0].text()).toContain('Asia/Novosibirsk');
        expect(tr[1].text()).toContain('Москва');
        expect(tr[1].text()).toContain('Europe/Moscow');
        expect(tr[2].text()).toContain('Омск');
        expect(tr[2].text()).toContain('не задан');
    });
    
    it("Отрисовка страницы без городов (гостевой режим)", () => {
        const wrapper = getWrapper();
        
        expect(wrapper.findComponent(GuestLayout).exists()).toBe(true);
        
        checkH1(wrapper);
        
        checkBreadCrumb(wrapper);
        
        // Таблица городов отсутствует
        const table = wrapper.find('table');
        expect(table.exists()).toBe(false);
        // На странице имеется запись
        expect(wrapper.text()).toContain('Ещё ни один город не добавлен');
    });
});
