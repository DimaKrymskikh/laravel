import { mount } from "@vue/test-utils";
import { router } from '@inertiajs/vue3';

import { messageEmptyTable } from '@/Services/Content/Trials/trials';
import Trials from "@/Pages/Auth/Trials/Trials.vue";
import BreadCrumb from '@/Components/Elements/BreadCrumb.vue';
import EyeSvg from '@/Components/Svg/EyeSvg.vue';

import { AuthLayoutStub } from '@/__tests__/stubs/layout';
import { quizzes } from '@/__tests__/data/quizzes/quizzes';

// Делаем заглушку для Head
vi.mock('@inertiajs/vue3', async () => {
    const actual = await vi.importActual("@inertiajs/vue3");
    return {
        ...actual,
        router: {
            put: vi.fn()
        },
        Head: vi.fn()
    };
});

const titlePage = 'Опросы';

const getWrapper = function(quizzes = []) {
    return mount(Trials, {
            props: {
                errors: {},
                quizzes
            },
            global: {
                stubs: {
                    AuthLayout: AuthLayoutStub
                }
            }
        });
};
    
const checkH1 = function(wrapper) {
    const h1 = wrapper.get('h1');
    expect(h1.text()).toBe(titlePage);
};

const checkBreadCrumb = function(wrapper) {
    const breadCrumb = wrapper.getComponent(BreadCrumb);
    expect(breadCrumb.isVisible()).toBe(true);
    const li = breadCrumb.findAll('li');
    expect(li.length).toBe(2);
    expect(li[0].find('a[href="/"]').exists()).toBe(true);
    expect(li[0].text()).toBe('Главная страница');
    expect(li[1].find('a').exists()).toBe(false);
    expect(li[1].text()).toBe(titlePage);
};

const checkThead = function(table) {
        const thead = table.get('thead');
        expect(thead.isVisible()).toBe(true);
        const th = thead.findAll('th');
        expect(th.length).toBe(5);
        expect(th[0].text()).toBe('#');
        expect(th[1].text()).toBe('Название');
        expect(th[2].text()).toBe('Описание');
        expect(th[3].text()).toBe('Прод-ть');
        expect(th[4].text()).toBe('');
};

describe("@/Pages/Auth/Trials/Trials.vue", () => {
    it("Отрисовка страницы 'Опросы' при наличии опросов", async () => {
        const wrapper = getWrapper(quizzes);
        
        // Проверяем заголовок
        checkH1(wrapper);
        
        // Проверяем хлебные крошки
        checkBreadCrumb(wrapper);
        
        // Проверяем таблицу опросов
        const table = wrapper.get('table');
        expect(table.isVisible()).toBe(true);
        // На странице отсутствует запись
        expect(wrapper.text()).not.toContain(messageEmptyTable);
        
        // Проверяем заголовок таблицы опросов
        checkThead(table);
        
        // Проверяем тело таблицы городов
        const tbody = table.get('tbody');
        expect(tbody.isVisible()).toBe(true);
        const trs = tbody.findAll('tr');
        expect(trs.length).toBe(quizzes.length);
        
        const tr1 = trs[1];
        const tds = tr1.findAll('td');
        const quiz = quizzes[1];
        expect(tds.length).toBe(5);
        expect(tds[0].text()).toBe('2');
        
        expect(tds[1].text()).toBe(quiz.title);
        expect(tds[2].text()).toBe(quiz.description);
        expect(tds[3].text()).toBe(quiz.lead_time + ' минут');
        
        const aEyeSvg = tds[4].get('a');
        expect(aEyeSvg.attributes('href')).toBe(`/trials/${quiz.id}`);
        expect(aEyeSvg.getComponent(EyeSvg).exists()).toBe(true);
        
        // На странице отсутствует запись
        expect(wrapper.text()).not.toContain(messageEmptyTable);
    });
    
    it("Отрисовка страницы 'Опросы' без опросов", () => {
        const wrapper = getWrapper();
        
        // Проверяем заголовок
        checkH1(wrapper);
        
        // Проверяем хлебные крошки
        checkBreadCrumb(wrapper);
        
        // Таблица опросов не содержит тела
        const table = wrapper.find('table');
        expect(table.exists()).toBe(true);
        const tbody = table.find('tbody');
        expect(tbody.exists()).toBe(false);
        // Проверяем заголовок таблицы опросов
        checkThead(table);
        
        // На странице присутствует запись
        expect(wrapper.text()).toContain(messageEmptyTable);
    });
});
