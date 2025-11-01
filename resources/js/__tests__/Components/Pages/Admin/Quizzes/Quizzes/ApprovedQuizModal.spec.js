import { mount } from "@vue/test-utils";
import { router } from '@inertiajs/vue3';

import { app } from '@/Services/app';
import { approvedQuiz } from '@/Services/Content/Quizzes/quizzes';
import ApprovedQuizModal from '@/Components/Pages/Admin/Quizzes/Quizzes/ApprovedQuizModal.vue';

import { quizzes } from '@/__tests__/data/quizzes/quizzes';
import { checkBaseModal } from '@/__tests__/methods/checkBaseModal';
import { eventCurrentTargetClassListContainsFalse } from '@/__tests__/fake/Event';

vi.mock('@inertiajs/vue3');
        
const hideModal = vi.spyOn(approvedQuiz, 'hide');

const getWrapper = function(quiz) {
    return mount(ApprovedQuizModal, {
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

describe("@/Components/Pages/Admin/Quizzes/Quizzes/ApprovedQuizModal.vue", () => {
    beforeEach(() => {
        app.isRequest = false;
        approvedQuiz.isApproved = false;
    });
    
    it("Монтирование компоненты ApprovedQuizModal (isRequest: false, статус 'утверждён')", async () => {
        approvedQuiz.isApproved = true;
        const wrapper = getWrapper(quizzes[3]);
        
        await checkBaseModal.hideBaseModal(wrapper, hideModal);
        await checkBaseModal.submitRequestInBaseModal(wrapper, router.put);
    });
    
    it("Монтирование компоненты ApprovedQuizModal (isRequest: true, статус 'утверждён')", async () => {
        approvedQuiz.isApproved = true;
        app.isRequest = true;
        const wrapper = getWrapper(quizzes[3]);
        
        await checkBaseModal.notHideBaseModal(wrapper, hideModal);
        await checkBaseModal.notSubmitRequestInBaseModal(wrapper, router.put);
    });
    
    it("Функция handlerApprovedQuiz вызывает router.put с нужными параметрами (статус 'утверждён')", () => {
        approvedQuiz.isApproved = true;
        const wrapper = getWrapper(quizzes[2]);
        
        wrapper.vm.handlerApprovedQuiz(eventCurrentTargetClassListContainsFalse);
        
        expect(router.put).toHaveBeenCalledTimes(1);
        expect(router.put).toHaveBeenCalledWith(`/admin/quizzes/${approvedQuiz.id}/cancel_status`, {}, options);
    });
    
    it("Функция handlerApprovedQuiz вызывает router.put с нужными параметрами (нет статуса 'утверждён')", () => {
        const wrapper = getWrapper(quizzes[0]);
        
        wrapper.vm.handlerApprovedQuiz(eventCurrentTargetClassListContainsFalse);
        
        expect(router.put).toHaveBeenCalledTimes(1);
        expect(router.put).toHaveBeenCalledWith(`/admin/quizzes/${approvedQuiz.id}/set_status`, {status: 'approved'}, options);
    });
    
    it("Проверка функции onSuccess", async () => {
        const wrapper = getWrapper(quizzes[3]);
        
        expect(hideModal).not.toHaveBeenCalled();
        wrapper.vm.onSuccess();
        
        expect(hideModal).toHaveBeenCalledTimes(1);
    });
});
