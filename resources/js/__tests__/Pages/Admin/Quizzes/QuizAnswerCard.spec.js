import { mount } from "@vue/test-utils";

import { messageEmptyTable } from '@/Services/Content/Quizzes/quizItemCard';
import QuizAnswerCard from "@/Pages/Admin/Quizzes/QuizAnswerCard.vue";
import BreadCrumb from '@/Components/Elements/BreadCrumb.vue';
import QuizAnswerCardHeader from '@/Components/Pages/Admin/Quizzes/QuizAnswerCard/QuizAnswerCardHeader.vue';
import CheckSvg from '@/Components/Svg/CheckSvg.vue';

import { AdminLayoutStub } from '@/__tests__/stubs/layout';
import { answerTrue, answerFalse } from '@/__tests__/data/quizzes/quizAnswers';

vi.mock('@inertiajs/vue3', async () => {
    const actual = await vi.importActual("@inertiajs/vue3");
    return {
        ...actual,
        Head: vi.fn()
    };
});

const getWrapper = function(quizAnswer) {
    return mount(QuizAnswerCard, {
            props: {
                errors: {},
                quizAnswer
            },
            global: {
                stubs: {
                    AdminLayout: AdminLayoutStub
                }
            }
        });
};
    
const checkHead = function(wrapper) {
    const quizAnswerCardHeader = wrapper.getComponent(QuizAnswerCardHeader);
    expect(quizAnswerCardHeader.isVisible()).toBe(true);
    expect(quizAnswerCardHeader.props('quizAnswer')).toBe(wrapper.vm.props.quizAnswer);
    
    const h1 = wrapper.get('h1');
    expect(h1.text()).toBe(wrapper.vm.titlePage);
};

const checkBreadCrumb = function(wrapper) {
    const breadCrumb = wrapper.getComponent(BreadCrumb);
    expect(breadCrumb.isVisible()).toBe(true);
    const li = breadCrumb.findAll('li');
    expect(li.length).toBe(5);
    expect(li[0].find('a[href="/admin"]').exists()).toBe(true);
    expect(li[0].text()).toBe('Страница админа');
    expect(li[1].find('a[href="/admin/quizzes"]').exists()).toBe(true);
    expect(li[1].text()).toBe('Опросы');
    expect(li[2].find('a[href="/admin/quizzes/' + wrapper.vm.props.quizAnswer.quiz_item.quiz.id + '"]').exists()).toBe(true);
    expect(li[2].text()).toBe('Карточка опроса');
    expect(li[3].find('a[href="/admin/quiz_items/' + wrapper.vm.props.quizAnswer.quiz_item.id + '"]').exists()).toBe(true);
    expect(li[3].text()).toBe('Карточка вопроса');
    expect(li[4].find('a').exists()).toBe(false);
    expect(li[4].text()).toBe(wrapper.vm.titlePage);
};

describe("@/Pages/Admin/Quizzes/QuizAnswerCard.vue", () => {
    it("Отрисовка страницы 'Карточка ответа' (правильный ответ)", () => {
        const wrapper = getWrapper(answerTrue);
        
        checkBreadCrumb(wrapper);
        
        checkHead(wrapper);
        
        expect(wrapper.findComponent(CheckSvg).exists()).toBe(true);
    });
    
    it("Отрисовка страницы 'Карточка ответа' (неверный ответ)", () => {
        const wrapper = getWrapper(answerFalse);
        
        checkBreadCrumb(wrapper);
        
        checkHead(wrapper);
        
        expect(wrapper.findComponent(CheckSvg).exists()).toBe(false);
    });
});
