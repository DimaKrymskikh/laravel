import { mount } from "@vue/test-utils";
import { router } from '@inertiajs/vue3';

import { app } from '@/Services/app';
import { removedQuizItem } from '@/Services/Content/Quizzes/quizCard';
import RemovedQuizItemModal from '@/Components/Pages/Admin/Quizzes/QuizCard/RemovedQuizItemModal.vue';

import { quizItems } from '@/__tests__/data/quizzes/quizItems';
import { checkBaseModal } from '@/__tests__/methods/checkBaseModal';
import { eventCurrentTargetClassListContainsFalse } from '@/__tests__/fake/Event';

vi.mock('@inertiajs/vue3');
        
const hideModal = vi.spyOn(removedQuizItem, 'hide');

const getWrapper = function(quizItem) {
    return mount(RemovedQuizItemModal, {
        props: {
            quizItem
        }
    });
};
        
const options = {
    onBefore: expect.anything(),
    onSuccess: expect.anything(),
    onError: expect.anything(),
    onFinish: expect.anything()
};

describe("@/Components/Pages/Admin/Quizzes/QuizCard/RemovedQuizItemModal.vue", () => {
    beforeEach(() => {
        app.isRequest = false;
        removedQuizItem.isRemoved = false;
    });
    
    it("Монтирование компоненты RemovedQuizItemModal (isRequest: false, статус 'удалён')", async () => {
        removedQuizItem.isRemoved = true;
        const wrapper = getWrapper(quizItems[2]);
        
        await checkBaseModal.hideBaseModal(wrapper, hideModal);
        await checkBaseModal.submitRequestInBaseModal(wrapper, router.put);
    });
    
    it("Монтирование компоненты RemovedQuizItemModal (isRequest: true, статус 'удалён')", async () => {
        removedQuizItem.isRemoved = true;
        app.isRequest = true;
        const wrapper = getWrapper(quizItems[2]);
        
        await checkBaseModal.notHideBaseModal(wrapper, hideModal);
        await checkBaseModal.notSubmitRequestInBaseModal(wrapper, router.put);
    });
    
    it("Функция handlerRemovedQuizItem вызывает router.put с нужными параметрами (статус 'удалён')", () => {
        removedQuizItem.isRemoved = true;
        const wrapper = getWrapper(quizItems[2]);
        
        wrapper.vm.handlerRemovedQuizItem(eventCurrentTargetClassListContainsFalse);
        
        expect(router.put).toHaveBeenCalledTimes(1);
        expect(router.put).toHaveBeenCalledWith(`/admin/quiz_items/${removedQuizItem.id}/cancel_status`, {}, options);
    });
    
    it("Функция handlerRemovedQuizItem вызывает router.put с нужными параметрами (нет статуса 'удалён')", () => {
        const wrapper = getWrapper(quizItems[2]);
        
        wrapper.vm.handlerRemovedQuizItem(eventCurrentTargetClassListContainsFalse);
        
        expect(router.put).toHaveBeenCalledTimes(1);
        expect(router.put).toHaveBeenCalledWith(`/admin/quiz_items/${removedQuizItem.id}/set_status`, {status: 'removed'}, options);
    });
    
    it("Проверка функции onSuccess", async () => {
        const wrapper = getWrapper(quizItems[2]);
        
        expect(hideModal).not.toHaveBeenCalled();
        wrapper.vm.onSuccess();
        
        expect(hideModal).toHaveBeenCalledTimes(1);
    });
});
