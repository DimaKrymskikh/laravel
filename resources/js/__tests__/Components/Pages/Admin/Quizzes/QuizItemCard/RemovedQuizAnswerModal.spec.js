import { mount } from "@vue/test-utils";
import { router } from '@inertiajs/vue3';

import { app } from '@/Services/app';
import { removedQuizAnswer } from '@/Services/Content/Quizzes/quizItemCard';
import RemovedQuizAnswerModal from '@/Components/Pages/Admin/Quizzes/QuizItemCard/RemovedQuizAnswerModal.vue';

import { quizItemWithAnswers } from '@/__tests__/data/quizzes/quizItems';
import { checkBaseModal } from '@/__tests__/methods/checkBaseModal';
import { eventCurrentTargetClassListContainsFalse } from '@/__tests__/fake/Event';

vi.mock('@inertiajs/vue3');
        
const hideModal = vi.spyOn(removedQuizAnswer, 'hide');

const getWrapper = function() {
    return mount(RemovedQuizAnswerModal, {
        props: {
            quizItem: quizItemWithAnswers
        }
    });
};
        
describe("@/Components/Pages/Admin/Quizzes/QuizItemCard/RemovedQuizAnswerModal.vue", () => {
    beforeEach(() => {
        app.isRequest = false;
    });
    
    it("Монтирование компоненты RemoveQuizAnswerModal (isRequest: false)", async () => {
        const wrapper = getWrapper();
        
        await checkBaseModal.hideBaseModal(wrapper, hideModal);
        await checkBaseModal.submitRequestInBaseModal(wrapper, router.delete);
    });
    
    it("Монтирование компоненты AddQuizAnswerModal (isRequest: true)", async () => {
        app.isRequest = true;
        const wrapper = getWrapper();
        
        await checkBaseModal.notHideBaseModal(wrapper, hideModal);
        await checkBaseModal.notSubmitRequestInBaseModal(wrapper, router.post);
    });
    
    it("Функция handlerRemoveQuizAnswer вызывает router.delete с нужными параметрами", () => {
        const wrapper = getWrapper();
        
        const options = {
            onBefore: expect.anything(),
            onSuccess: expect.anything(),
            onError: expect.anything(),
            onFinish: expect.anything()
        };

        wrapper.vm.handlerRemoveQuizAnswer(eventCurrentTargetClassListContainsFalse);
        
        expect(router.delete).toHaveBeenCalledTimes(1);
        expect(router.delete).toHaveBeenCalledWith(`/admin/quiz_answers/${wrapper.vm.removedQuizAnswer.id}`, options);
    });
    
    it("Проверка функции onSuccess", async () => {
        const wrapper = getWrapper();
        
        expect(hideModal).not.toHaveBeenCalled();
        wrapper.vm.onSuccess();
        
        expect(hideModal).toHaveBeenCalledTimes(1);
    });
});
