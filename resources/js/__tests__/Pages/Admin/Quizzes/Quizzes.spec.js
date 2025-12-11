import { mount } from "@vue/test-utils";
import { router } from '@inertiajs/vue3';

import { approvedQuiz, removedQuiz } from '@/Services/Content/Quizzes/quizzes';
import { messageEmptyTable } from '@/Services/Content/Quizzes/quizzes';
import Quizzes from "@/Pages/Admin/Quizzes/Quizzes.vue";
import BreadCrumb from '@/Components/Elements/BreadCrumb.vue';
import SimpleInput from '@/Components/Elements/Form/Input/SimpleInput.vue';
import ApprovedQuizModal from '@/Components/Pages/Admin/Quizzes/Quizzes/ApprovedQuizModal.vue';
import RemovedQuizModal from '@/Components/Pages/Admin/Quizzes/Quizzes/RemovedQuizModal.vue';
import EyeSvg from '@/Components/Svg/EyeSvg.vue';
import QuizDescriptionColumn from '@/Components/Pages/Admin/Quizzes/Quizzes/QuizDescriptionColumn.vue';
import QuizLeadTimeColumn from '@/Components/Pages/Admin/Quizzes/Quizzes/QuizLeadTimeColumn.vue';
import QuizTitleColumn from '@/Components/Pages/Admin/Quizzes/Quizzes/QuizTitleColumn.vue';
import ApprovedQuizColumn from '@/Components/Pages/Admin/Quizzes/Quizzes/ApprovedQuizColumn.vue';
import RemovedQuizColumn from '@/Components/Pages/Admin/Quizzes/Quizzes/RemovedQuizColumn.vue';

import { AdminLayoutStub } from '@/__tests__/stubs/layout';
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
    return mount(Quizzes, {
            props: {
                errors: {},
                quizzes
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
    expect(h1.text()).toBe(titlePage);
};

const checkBreadCrumb = function(wrapper) {
    const breadCrumb = wrapper.getComponent(BreadCrumb);
    expect(breadCrumb.isVisible()).toBe(true);
    const li = breadCrumb.findAll('li');
    expect(li.length).toBe(2);
    expect(li[0].find('a[href="/admin"]').exists()).toBe(true);
    expect(li[0].text()).toBe('Страница админа');
    expect(li[1].find('a').exists()).toBe(false);
    expect(li[1].text()).toBe(titlePage);
};

const checkThead = function(table) {
        const thead = table.get('thead');
        expect(thead.isVisible()).toBe(true);
        const th = thead.findAll('th');
        expect(th.length).toBe(8);
        expect(th[0].text()).toBe('#');
        expect(th[1].text()).toBe('Название');
        expect(th[2].text()).toBe('Описание');
        expect(th[3].text()).toBe('Прод-ть');
        expect(th[4].text()).toBe('Статус');
        expect(th[5].text()).toBe('');
        expect(th[6].text()).toBe('');
        expect(th[7].text()).toBe('');
};

describe("@/Pages/Admin/Quizzes/Quizzes.vue", () => {
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
        expect(tr1.findComponent(QuizDescriptionColumn).exists()).toBe(true);
        expect(tr1.findComponent(QuizTitleColumn).exists()).toBe(true);
        expect(tr1.findComponent(QuizLeadTimeColumn).exists()).toBe(true);
        expect(tr1.findComponent(ApprovedQuizColumn).exists()).toBe(true);
        expect(tr1.findComponent(RemovedQuizColumn).exists()).toBe(true);
        
        const tds = tr1.findAll('td');
        const quiz = quizzes[1];
        expect(tds.length).toBe(11);
        expect(tds[0].text()).toBe('2');
        
        expect(tds[7].text()).toBe(quiz.status.name);
        expect(tds[7].classes()).toContain(quiz.status.style);
        
        const aEyeSvg = tds[8].get('a');
        expect(aEyeSvg.attributes('href')).toBe(`/admin/quizzes/${quiz.id}`);
        expect(aEyeSvg.getComponent(EyeSvg).exists()).toBe(true);
        
        // Странное поведение DOMWrapper
        // Компоненты ApprovedQuizColumn и RemovedQuizColumn содержат tds[9] и tds[10], соответственно,
        // а тест показывает, что наоборот.
        expect(tds[9].findComponent(ApprovedQuizColumn).exists()).toBe(true);
        expect(tds[10].findComponent(RemovedQuizColumn).exists()).toBe(true);
        
        expect(wrapper.findComponent(ApprovedQuizModal).exists()).toBe(false);
        expect(wrapper.findComponent(RemovedQuizModal).exists()).toBe(false);
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
        
        expect(wrapper.findComponent(ApprovedQuizModal).exists()).toBe(false);
        expect(wrapper.findComponent(RemovedQuizModal).exists()).toBe(false);
    });
    
    it("Отрисовка страницы 'Опросы' с модальным окном ApprovedQuizModal", () => {
        approvedQuiz.isShow = true;
        const wrapper = getWrapper(quizzes);
        
        expect(wrapper.findComponent(ApprovedQuizModal).exists()).toBe(true);
    });
    
    it("Отрисовка страницы 'Опросы' с модальным окном RemovedQuizModal", () => {
        removedQuiz.isShow = true;
        const wrapper = getWrapper(quizzes);
        
        expect(wrapper.findComponent(RemovedQuizModal).exists()).toBe(true);
    });
});
