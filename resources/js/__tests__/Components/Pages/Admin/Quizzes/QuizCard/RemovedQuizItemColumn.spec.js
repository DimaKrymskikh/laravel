import { mount } from "@vue/test-utils";

import { removedQuizItem } from '@/Services/Content/Quizzes/quizCard';
import RemovedQuizItemColumn from '@/Components/Pages/Admin/Quizzes/QuizCard/RemovedQuizItemColumn.vue';
import RemovedQuizItemModal from '@/Components/Pages/Admin/Quizzes/QuizCard/RemovedQuizItemModal.vue';
import ReplySvg from '@/Components/Svg/ReplySvg.vue';
import TrashSvg from '@/Components/Svg/TrashSvg.vue';

import { quizItems } from '@/__tests__/data/quizzes/quizItems';

const getWrapper = function(quizItem) {
    return mount(RemovedQuizItemColumn, {
        props: {
            quizItem
        }
    });
};

const checkShowModal = (quizItem) => {
        expect(removedQuizItem.id).toBe(quizItem.id);
        expect(removedQuizItem.description).toBe(quizItem.description);
        expect(removedQuizItem.quizTitle).toBe(quizItem.quiz.title);
        expect(removedQuizItem.isShow).toBe(true);
};

describe("@/Components/Pages/Admin/Quizzes/QuizCard/RemovedQuizItemColumn.vue", () => {
    beforeEach(() => {
        removedQuizItem.isShow = false;
    });
    
    it("Отрисовка блока RemovedQuizItemColumn (статус 'удалён')", async () => {
        const quizItem = quizItems[2];
        const wrapper = getWrapper(quizItem);
        
        const replySvg = wrapper.findComponent(ReplySvg);
        expect(replySvg.exists()).toBe(true);
        expect(wrapper.findComponent(TrashSvg).exists()).toBe(false);
        
        await replySvg.trigger('click');
        expect(removedQuizItem.isRemoved).toBe(true);
        checkShowModal(quizItem);
    });
    
    it("Отрисовка блока RemovedQuizItemColumn (нет статуса 'удалён')", async () => {
        const quizItem = quizItems[0];
        const wrapper = getWrapper(quizItem);
        
        const trashSvg = wrapper.findComponent(TrashSvg);
        expect(trashSvg.exists()).toBe(true);
        expect(wrapper.findComponent(ReplySvg).exists()).toBe(false);
        
        await trashSvg.trigger('click');
        expect(removedQuizItem.isRemoved).toBe(false);
        checkShowModal(quizItem);
    });
    
    it("Проверка onUpdated", async () => {
        const wrapper = getWrapper(quizItems[0]);
        expect(wrapper.vm.isRemoved).toBe(false);
        
        await wrapper.setProps({ quizItem: quizItems[2] });
        expect(wrapper.vm.isRemoved).toBe(true);
    });
});
