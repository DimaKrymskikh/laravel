import { mount } from "@vue/test-utils";

import { messageEmptyTable } from '@/Services/Content/Quizzes/quizItemCard';
import QuizItemCard from "@/Pages/Admin/Quizzes/QuizItemCard.vue";
import RemovedQuizAnswerModal from '@/Components/Pages/Admin/Quizzes/QuizItemCard/RemovedQuizAnswerModal.vue';
import BreadCrumb from '@/Components/Elements/BreadCrumb.vue';
import TrashSvg from '@/Components/Svg/TrashSvg.vue';

import { AdminLayoutStub } from '@/__tests__/stubs/layout';
import { quizItemWithAnswers, quizItemWithoutAnswers } from '@/__tests__/data/quizzes/quizItems';

// Делаем заглушку для Head
vi.mock('@inertiajs/vue3', async () => {
    const actual = await vi.importActual("@inertiajs/vue3");
    return {
        ...actual,
        Head: vi.fn()
    };
});

const getWrapper = function(quizItem) {
    return mount(QuizItemCard, {
            props: {
                errors: {},
                quizItem
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
    expect(h1.text()).toBe(wrapper.vm.titlePage);
};

const checkBreadCrumb = function(wrapper) {
    const breadCrumb = wrapper.getComponent(BreadCrumb);
    expect(breadCrumb.isVisible()).toBe(true);
    const li = breadCrumb.findAll('li');
    expect(li.length).toBe(4);
    expect(li[0].find('a[href="/admin"]').exists()).toBe(true);
    expect(li[0].text()).toBe('Страница админа');
    expect(li[1].find('a[href="/admin/quizzes"]').exists()).toBe(true);
    expect(li[1].text()).toBe('Опросы');
    expect(li[2].find('a[href="/admin/quizzes/' + wrapper.vm.props.quizItem.quiz.id + '"]').exists()).toBe(true);
    expect(li[2].text()).toBe('Карточка опроса');
    expect(li[3].find('a').exists()).toBe(false);
    expect(li[3].text()).toBe(wrapper.vm.titlePage);
};

const checkThead = function(table) {
        const thead = table.get('thead');
        expect(thead.isVisible()).toBe(true);
        const th = thead.findAll('th');
        expect(th.length).toBe(6);
        expect(th[0].text()).toBe('#');
        expect(th[1].text()).toBe('Ответ');
        expect(th[2].text()).toBe('Правильный');
        expect(th[3].text()).toBe('Приоритет');
        expect(th[4].text()).toBe('');
        expect(th[5].text()).toBe('');
};

describe("@/Services/Content/Quizzes/quizItemCard", () => {
    it("Отрисовка страницы 'Карточка вопроса' при наличии ответов", async () => {
        const wrapper = getWrapper(quizItemWithAnswers);
        
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
        expect(trs.length).toBe(2);
    });
    
    it("Отрисовка страницы 'Карточка вопроса' без ответов", () => {
        const wrapper = getWrapper(quizItemWithoutAnswers);
        
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
    
    it("Клик по TrashSvg открывает модальное окно", async () => {
        const wrapper = getWrapper(quizItemWithAnswers);
        expect(wrapper.findComponent(RemovedQuizAnswerModal).exists()).toBe(false);
        
        const tbody = wrapper.get('tbody');
        const trs = tbody.findAll('tr');
        expect(trs.length).toBe(2);
        
        const trashSvg = trs[0].findComponent(TrashSvg);
        await trashSvg.trigger('click');
        
        const removeQuizAnswerModal = wrapper.findComponent(RemovedQuizAnswerModal);
        expect(removeQuizAnswerModal.exists()).toBe(true);
        expect(removeQuizAnswerModal.props('quizItem')).toBe(wrapper.vm.props.quizItem);
    });
});
