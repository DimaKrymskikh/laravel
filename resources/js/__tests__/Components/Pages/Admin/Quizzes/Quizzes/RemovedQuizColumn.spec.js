import { mount } from "@vue/test-utils";

import { removedQuiz } from '@/Services/Content/Quizzes/quizzes';
import RemovedQuizColumn from '@/Components/Pages/Admin/Quizzes/Quizzes/RemovedQuizColumn.vue';
import ReplySvg from '@/Components/Svg/ReplySvg.vue';
import TrashSvg from '@/Components/Svg/TrashSvg.vue';
import LockedTrashSvg from '@/Components/Svg/Locked/LockedTrashSvg.vue';

import { quizzes } from '@/__tests__/data/quizzes/quizzes';

const getWrapper = function(quiz) {
    return mount(RemovedQuizColumn, {
        props: {
            quiz
        }
    });
};

const checkShowModal = (quiz) => {
        expect(removedQuiz.id).toBe(quiz.id);
        expect(removedQuiz.title).toBe(quiz.title);
        expect(removedQuiz.isShow).toBe(true);
};

describe("@/Components/Pages/Admin/Quizzes/Quizzes/RemovedQuizColumn.vue", () => {
    beforeEach(() => {
        removedQuiz.isShow = false;
    });
    
    it("Отрисовка блока RemovedQuizColumn (статус 'удалён')", async () => {
        const quiz = quizzes[2];
        const wrapper = getWrapper(quiz);
        const showModal = vi.spyOn(wrapper.vm, 'showModal');
        
        const replySvg = wrapper.findComponent(ReplySvg);
        expect(replySvg.exists()).toBe(true);
        expect(wrapper.findComponent(TrashSvg).exists()).toBe(false);
        
        await replySvg.trigger('click');
        expect(showModal).toHaveBeenCalledTimes(1);
        expect(removedQuiz.isRemoved).toBe(true);
        checkShowModal(quiz);
    });
    
    it("Отрисовка блока RemovedQuizColumn (статус 'в работе')", async () => {
        const quiz = quizzes[0];
        const wrapper = getWrapper(quiz);
        const showModal = vi.spyOn(wrapper.vm, 'showModal');
        
        const trashSvg = wrapper.findComponent(TrashSvg);
        expect(trashSvg.exists()).toBe(true);
        expect(wrapper.findComponent(ReplySvg).exists()).toBe(false);
        
        await trashSvg.trigger('click');
        expect(showModal).toHaveBeenCalledTimes(1);
        expect(removedQuiz.isRemoved).toBe(false);
        checkShowModal(quiz);
    });
    
    it("Отрисовка блока RemovedQuizColumn (статус 'утверждён')", async () => {
        const quiz = quizzes[3];
        const wrapper = getWrapper(quiz);
        const showModal = vi.spyOn(wrapper.vm, 'showModal');
        
        const lockedTrashSvg = wrapper.findComponent(LockedTrashSvg);
        expect(lockedTrashSvg.exists()).toBe(true);
        
        await lockedTrashSvg.trigger('click');
        expect(showModal).not.toHaveBeenCalled();
    });
    
    it("Проверка onUpdated", async () => {
        const wrapper = getWrapper(quizzes[0]);
        expect(wrapper.vm.isRemoved).toBe(false);
        expect(wrapper.vm.isEditable).toBe(true);
        
        await wrapper.setProps({ quiz: quizzes[2] });
        expect(wrapper.vm.isRemoved).toBe(true);
        expect(wrapper.vm.isEditable).toBe(false);
    });
});
