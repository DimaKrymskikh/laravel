import { mount } from "@vue/test-utils";

import { messageEmptyTable } from '@/Services/Content/Quizzes/quizCard';
import { removedQuizItem } from '@/Services/Content/Quizzes/quizCard';
import QuizCard from "@/Pages/Admin/Quizzes/QuizCard.vue";
import BreadCrumb from '@/Components/Elements/BreadCrumb.vue';
import PencilSvg from '@/Components/Svg/PencilSvg.vue';
import EyeSvg from '@/Components/Svg/EyeSvg.vue';
import RemovedQuizItemModal from '@/Components/Pages/Admin/Quizzes/QuizCard/RemovedQuizItemModal.vue';

import { AdminLayoutStub } from '@/__tests__/stubs/layout';
import { quizWithItems, quizWithoutItems } from '@/__tests__/data/quizzes/quizzes';

// Делаем заглушку для Head
vi.mock('@inertiajs/vue3', async () => {
    const actual = await vi.importActual("@inertiajs/vue3");
    return {
        ...actual,
        Head: vi.fn()
    };
});

const getWrapper = function(quiz) {
    return mount(QuizCard, {
            props: {
                errors: {},
                quiz
            },
            global: {
                stubs: {
                    AdminLayout: AdminLayoutStub
                }
            }
        });
};
    
const checkH1 = function(wrapper) {
    const h1 = wrapper.get('h1');
    expect(h1.text()).toBe(wrapper.vm.quiz.title);
};

const checkBreadCrumb = function(wrapper) {
    const breadCrumb = wrapper.getComponent(BreadCrumb);
    expect(breadCrumb.isVisible()).toBe(true);
    const li = breadCrumb.findAll('li');
    expect(li.length).toBe(3);
    expect(li[0].find('a[href="/admin"]').exists()).toBe(true);
    expect(li[0].text()).toBe('Страница админа');
    expect(li[1].find('a[href="/admin/quizzes"]').exists()).toBe(true);
    expect(li[1].text()).toBe('Опросы');
    expect(li[2].find('a').exists()).toBe(false);
    expect(li[2].text()).toBe(wrapper.vm.quiz.title);
};

const checkThead = function(table) {
        const thead = table.get('thead');
        expect(thead.isVisible()).toBe(true);
        const th = thead.findAll('th');
        expect(th.length).toBe(5);
        expect(th[0].text()).toBe('#');
        expect(th[1].text()).toBe('Вопрос');
        expect(th[2].text()).toBe('Статус');
        expect(th[3].text()).toBe('');
        expect(th[4].text()).toBe('');
};

describe("@/Pages/Admin/Quizzes/QuizCard.vue", () => {
    beforeEach(() => {
        removedQuizItem.isShow = false;
    });
    
    it("Отрисовка страницы 'Карточка опроса' при наличии вопросов", async () => {
        const wrapper = getWrapper(quizWithItems);
        
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
        const tr = tbody.findAll('tr');
        expect(tr.length).toBe(quizWithItems.quiz_items.length);
        
        const quizItem = quizWithItems.quiz_items[0];
        const tds = tr[0].findAll('td');
        expect(tds.length).toBe(6);
        expect(tds[0].text()).toBe('1');
        expect(tds[1].text()).toBe(quizItem.description);
        expect(tds[2].getComponent(PencilSvg).props('title')).toBe('Изменить текст вопроса');
        expect(tds[3].text()).toBe(quizItem.status.name);
        expect(tds[4].get('a').attributes('href')).toBe('/admin/quiz_items/' + quizItem.id);
        expect(tds[4].getComponent(EyeSvg).props('title')).toBe('Открыть карточку вопроса');
        
        expect(wrapper.findComponent(RemovedQuizItemModal).exists()).toBe(false);
    });
    
    it("Отрисовка страницы 'Карточка опроса' без вопросов", () => {
        const wrapper = getWrapper(quizWithoutItems);
        
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
        
        expect(wrapper.findComponent(RemovedQuizItemModal).exists()).toBe(false);
    });
    
    it("Отрисовка страницы 'Карточка опроса' с модальным окном RemovedQuizItemModal", () => {
        removedQuizItem.isShow = true;
        const wrapper = getWrapper(quizWithItems);
        
        expect(wrapper.findComponent(RemovedQuizItemModal).exists()).toBe(true);
    });
});
