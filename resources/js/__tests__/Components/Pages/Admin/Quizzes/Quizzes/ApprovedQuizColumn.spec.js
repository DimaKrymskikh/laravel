import { mount } from "@vue/test-utils";

import { approvedQuiz } from '@/Services/Content/Quizzes/quizzes';
import ApprovedQuizColumn from '@/Components/Pages/Admin/Quizzes/Quizzes/ApprovedQuizColumn.vue';
import AppIndicatorSvg from '@/Components/Svg/AppIndicatorSvg.vue';

import { quizzes } from '@/__tests__/data/quizzes/quizzes';

const getWrapper = function(quiz) {
    return mount(ApprovedQuizColumn, {
        props: {
            quiz
        }
    });
};

const checkShowModal = (quiz) => {
        expect(approvedQuiz.id).toBe(quiz.id);
        expect(approvedQuiz.title).toBe(quiz.title);
        expect(approvedQuiz.isShow).toBe(true);
};

describe("@/Components/Pages/Admin/Quizzes/Quizzes/ApprovedQuizColumn.vue", () => {
    beforeEach(() => {
        approvedQuiz.isShow = false;
    });
    
    it("Отрисовка блока ApprovedQuizColumn (статус 'утверждён')", async () => {
        const quiz = quizzes[3];
        const wrapper = getWrapper(quiz);
        
        const appIndicatorSvg = wrapper.findComponent(AppIndicatorSvg);
        expect(appIndicatorSvg.exists()).toBe(true);
        
        await appIndicatorSvg.trigger('click');
        expect(approvedQuiz.isApproved).toBe(true);
        checkShowModal(quiz);
    });
    
    it("Отрисовка блока ApprovedQuizColumn (статус 'готов')", async () => {
        const quiz = quizzes[1];
        const wrapper = getWrapper(quiz);
        
        const appIndicatorSvg = wrapper.findComponent(AppIndicatorSvg);
        expect(appIndicatorSvg.exists()).toBe(true);
        
        await appIndicatorSvg.trigger('click');
        expect(approvedQuiz.isApproved).toBe(false);
        checkShowModal(quiz);
    });
    
    it("Отрисовка блока ApprovedQuizColumn (другие статусы)", async () => {
        const quiz = quizzes[0];
        const wrapper = getWrapper(quiz);
        
        const appIndicatorSvg = wrapper.findComponent(AppIndicatorSvg);
        expect(appIndicatorSvg.exists()).toBe(true);
        
        await appIndicatorSvg.trigger('click');
        expect(approvedQuiz.isApproved).toBe(false);
        expect(approvedQuiz.isShow).toBe(false);
    });
});
