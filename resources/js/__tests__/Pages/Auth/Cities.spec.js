import '@/bootstrap';
import { mount, flushPromises } from "@vue/test-utils";
import { router } from '@inertiajs/vue3';

import { setActivePinia, createPinia } from 'pinia';
import Cities from "@/Pages/Auth/Cities.vue";
import AuthLayout from '@/Layouts/AuthLayout.vue';
import BreadCrumb from '@/Components/Elements/BreadCrumb.vue';
import CheckCircleSvg from '@/Components/Svg/CheckCircleSvg.vue';
import PlusCircleSvg from '@/Components/Svg/PlusCircleSvg.vue';
import Spinner from '@/components/Svg/Spinner.vue';
import EchoAuth from '@/Components/Broadcast/EchoAuth.vue';
import { useAppStore } from '@/Stores/app';

import { cities_user } from '@/__tests__/data/cities';
import { AuthLayoutStub } from '@/__tests__/stubs/layout';

// Делаем заглушку для Head
vi.mock('@inertiajs/vue3', async () => {
    const actual = await vi.importActual("@inertiajs/vue3");
    return {
        ...actual,
        router: {
            post: vi.fn()
        },
        Head: vi.fn()
    };
});
        
const user = {
            id: 77,
            is_admin: false
        };

const getWrapper = function(app, cities = []) {
    return mount(Cities, {
            props: {
                errors: {},
                cities,
                user
            },
            global: {
                stubs: {
                    AuthLayout: AuthLayoutStub
                },
                provide: { app }
            }
        });
};

describe("@/Pages/Auth/Cities.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    
    it("Отрисовка страницы Cities для auth", () => {
        const app = useAppStore();
        
        const wrapper = getWrapper(app, cities_user);
        
        const authLayout = wrapper.findComponent(AuthLayout);
        expect(authLayout.props('user')).toStrictEqual(user);
        expect(authLayout.props('errors')).toStrictEqual({});
        
        // Отрисовывается заголовок страницы
        const h1 = wrapper.get('h1');
        expect(h1.text()).toBe(wrapper.vm.titlePage);
        
        // Отрисовываются хлебные крошки
        const breadCrumb = wrapper.getComponent(BreadCrumb);
        expect(breadCrumb.props('linksList')).toBe(wrapper.vm.linksList);
        
        // Проверяем таблицу городов
        const table = wrapper.get('table');
        expect(table.isVisible()).toBe(true);
        // На странице отсутствует запись
        expect(wrapper.text()).not.toContain('Ещё ни один город не добавлен');
        
        // Проверяем заголовок таблицы городов
        const thead = table.get('thead');
        expect(thead.isVisible()).toBe(true);
        const th = thead.findAll('th');
        expect(th.length).toBe(4);
        expect(th[0].text()).toBe('#');
        expect(th[1].text()).toBe('Город');
        expect(th[2].text()).toBe('Временной пояс');
        expect(th[3].text()).toBe('');
        
        // Проверяем тело таблицы городов
        const tbody = table.get('tbody');
        expect(tbody.isVisible()).toBe(true);
        const tr = tbody.findAll('tr');
        expect(tr.length).toBe(3);
        
        expect(tr[0].text()).toContain('Новосибирск');
        expect(tr[0].text()).toContain('Asia/Novosibirsk');
        expect(tr[0].findComponent(PlusCircleSvg).exists()).toBe(true);
        expect(tr[0].findComponent(CheckCircleSvg).exists()).toBe(false);
        
        expect(tr[1].text()).toContain('Москва');
        expect(tr[1].text()).toContain('Europe/Moscow');
        expect(tr[1].findComponent(PlusCircleSvg).exists()).toBe(false);
        expect(tr[1].findComponent(CheckCircleSvg).exists()).toBe(true);
        
        expect(tr[2].text()).toContain('Омск');
        expect(tr[2].text()).toContain('не задан');
        expect(tr[2].findComponent(PlusCircleSvg).exists()).toBe(true);
        expect(tr[2].findComponent(CheckCircleSvg).exists()).toBe(false);
        
        const echoAuth = wrapper.getComponent(EchoAuth);
        expect(echoAuth.props('user')).toStrictEqual(user);
        expect(echoAuth.props('events')).toStrictEqual(['AddCityInWeatherList']);
    });
    
    it("Отрисовка страницы Cities для auth (без городов)", () => {
        const app = useAppStore();
        
        const wrapper = getWrapper(app);
        
        const authLayout = wrapper.findComponent(AuthLayout);
        expect(authLayout.props('user')).toStrictEqual(user);
        expect(authLayout.props('errors')).toStrictEqual({});
        
        // Отрисовывается заголовок страницы
        const h1 = wrapper.get('h1');
        expect(h1.text()).toBe(wrapper.vm.titlePage);
        
        // Отрисовываются хлебные крошки
        const breadCrumb = wrapper.getComponent(BreadCrumb);
        expect(breadCrumb.props('linksList')).toBe(wrapper.vm.linksList);
        
        // Таблица городов отсутствует
        const table = wrapper.find('table');
        expect(table.exists()).toBe(false);
        // На странице имеется запись
        expect(wrapper.text()).toContain('Ещё ни один город не добавлен');
    });
    
    it("Добавление города в коллекцию пользователя", async () => {
        const app = useAppStore();
        
        const wrapper = getWrapper(app, cities_user);
        
        // Тело таблицы состоит из рядов с данными фильмов
        const table = wrapper.get('table');
        // Клик по тегу без родителя <td> не ломает функцию handlerTableChange
        expect(router.post).not.toHaveBeenCalled();
        await table.trigger('click');
        expect(router.post).not.toHaveBeenCalled();
        
        const tbody = table.get('tbody');
        const tbodyTr = tbody.findAll('tr');
        
        // Новосибирск
        const td0 = tbodyTr[0].findAll('td');
        // Отрисован плюс
        const plusCircleSvg = td0[3].findComponent(PlusCircleSvg);
        expect(plusCircleSvg.exists()).toBe(true);
        // Клик по плюсу отправляет запрос на сервер
        expect(router.post).not.toHaveBeenCalled();
        await plusCircleSvg.trigger('click');
        expect(router.post).toHaveBeenCalledTimes(1);
        
        router.post.mockClear();
        
        // Москва
        const td1 = tbodyTr[1].findAll('td');
        // Отрисована галочка
        const checkCircleSvg = td1[3].findComponent(CheckCircleSvg);
        expect(td1[3].findComponent(CheckCircleSvg).exists()).toBe(true);
        // Клик по галочке не отправляет запрос на сервер
        expect(router.post).not.toHaveBeenCalled();
        await plusCircleSvg.trigger('click');
        expect(router.post).not.toHaveBeenCalled();
    });
    
    it("Проверка появления спинера", async () => {
        const app = useAppStore();
        
        const wrapper = getWrapper(app, cities_user);
        
        expect(wrapper.vm.app.isRequest).toBe(false);
        
        const tbody = wrapper.get('table').get('tbody');
        const tbodyTr = tbody.findAll('tr');
        expect(tbodyTr.length).toBe(3);
        // Во всех строках таблицы спиннер отсутствует
        expect(tbodyTr[0].findComponent(Spinner).exists()).toBe(false);
        expect(tbodyTr[1].findComponent(Spinner).exists()).toBe(false);
        expect(tbodyTr[2].findComponent(Spinner).exists()).toBe(false);
        
        // Новосибирск
        const td0 = tbodyTr[0].findAll('td');
      
        const spanPlusCircleSvg = td0[3].getComponent(PlusCircleSvg).get('span');
        // Клик по spanPlusCircleSvg приводит к вызову функции addCity
        wrapper.vm.addCity(spanPlusCircleSvg.element);
        // Правильно находиться id города
        expect(wrapper.vm.cityId).toBe(String(cities_user[0].id));
        // В router.post должен измениться app.isRequest
        wrapper.vm.app.isRequest = true;
        
        await flushPromises();
        
        // Спиннер появился только в первой строке для Новосибирска
        expect(tbodyTr[0].findComponent(Spinner).exists()).toBe(true);
        expect(tbodyTr[1].findComponent(Spinner).exists()).toBe(false);
        expect(tbodyTr[2].findComponent(Spinner).exists()).toBe(false);
    });
});
