import { mount } from "@vue/test-utils";
import { router } from '@inertiajs/vue3';

import { app } from '@/Services/app';
import { removedQuiz } from '@/Services/Content/Quizzes/quizzes';
import RemovedQuizModal from '@/Components/Pages/Admin/Quizzes/Quizzes/RemovedQuizModal.vue';

import { quizzes } from '@/__tests__/data/quizzes/quizzes';
import { checkBaseModal } from '@/__tests__/methods/checkBaseModal';
import { eventCurrentTargetClassListContainsFalse } from '@/__tests__/fake/Event';

vi.mock('@inertiajs/vue3');
        
const hideModal = vi.spyOn(removedQuiz, 'hide');

const getWrapper = function(quiz) {
    return mount(RemovedQuizModal, {
        props: {
            quiz
        }
    });
};
        
const options = {
    onBefore: expect.anything(),
    onSuccess: expect.anything(),
    onError: expect.anything(),
    onFinish: expect.anything()
};

describe("@/Components/Pages/Admin/Quizzes/Quizzes/RemovedQuizModal.vue", () => {
    beforeEach(() => {
        app.isRequest = false;
        removedQuiz.isRemoved = false;
    });
    
    it("Монтирование компоненты RemovedQuizModal (isRequest: false, статус 'удалён')", async () => {
        removedQuiz.isRemoved = true;
        const wrapper = getWrapper(quizzes[2]);
        
        await checkBaseModal.hideBaseModal(wrapper, hideModal);
        await checkBaseModal.submitRequestInBaseModal(wrapper, router.put);
    });
    
    it("Монтирование компоненты RemovedQuizModal (isRequest: true, статус 'удалён')", async () => {
        removedQuiz.isRemoved = true;
        app.isRequest = true;
        const wrapper = getWrapper(quizzes[2]);
        
        await checkBaseModal.notHideBaseModal(wrapper, hideModal);
        await checkBaseModal.notSubmitRequestInBaseModal(wrapper, router.put);
    });
    
    it("Функция handlerRemovedQuiz вызывает router.put с нужными параметрами (статус 'удалён')", () => {
        removedQuiz.isRemoved = true;
        const wrapper = getWrapper(quizzes[2]);
        
        wrapper.vm.handlerRemovedQuiz(eventCurrentTargetClassListContainsFalse);
        
        expect(router.put).toHaveBeenCalledTimes(1);
        expect(router.put).toHaveBeenCalledWith(`/admin/quizzes/${removedQuiz.id}/cancel_status`, {}, options);
    });
    
    it("Функция handlerRemovedQuiz вызывает router.put с нужными параметрами (нет статуса 'удалён')", () => {
        const wrapper = getWrapper(quizzes[0]);
        
        wrapper.vm.handlerRemovedQuiz(eventCurrentTargetClassListContainsFalse);
        
        expect(router.put).toHaveBeenCalledTimes(1);
        expect(router.put).toHaveBeenCalledWith(`/admin/quizzes/${removedQuiz.id}/set_status`, {status: 'removed'}, options);
    });
    
    it("Проверка функции onSuccess", async () => {
        const wrapper = getWrapper(quizzes[2]);
        
        expect(hideModal).not.toHaveBeenCalled();
        wrapper.vm.onSuccess();
        
        expect(hideModal).toHaveBeenCalledTimes(1);
    });
});
