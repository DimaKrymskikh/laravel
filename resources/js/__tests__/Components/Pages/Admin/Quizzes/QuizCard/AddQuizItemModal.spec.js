import { mount } from "@vue/test-utils";
import { router } from '@inertiajs/vue3';

import { app } from '@/Services/app';
import { newQuizItem } from '@/Services/Content/Quizzes/quizCard';
import AddQuizItemModal from '@/Components/Pages/Admin/Quizzes/QuizCard/AddQuizItemModal.vue';

import { quizzes } from '@/__tests__/data/quizzes/quizzes';
import { checkBaseModal } from '@/__tests__/methods/checkBaseModal';
import { checkTextareas } from '@/__tests__/methods/checkTextareas';
import { eventCurrentTargetClassListContainsFalse } from '@/__tests__/fake/Event';

vi.mock('@inertiajs/vue3');
        
const hideModal = vi.spyOn(newQuizItem, 'hide');

const getWrapper = function() {
    return mount(AddQuizItemModal, {
        props: {
            quiz: quizzes[0]
        }
    });
};

const checkContent = function(wrapper) {
    // Проверка равенства переменных ref начальным данным
    expect(wrapper.vm.quizItemDescription).toBe(newQuizItem.description);

    // Заголовок модального окна задаётся
    expect(wrapper.text()).toContain('Добавление вопроса');
};
        
describe("@/Components/Pages/Admin/Quizzes/QuizCard/AddQuizItemModal.vue", () => {
    beforeEach(() => {
        app.isRequest = false;
    });
    
    it("Монтирование компоненты AddQuizItemModal (isRequest: false)", async () => {
        const wrapper = getWrapper();
        
        checkContent (wrapper);
        
        const textareas = checkTextareas.findNumberOfTextareasOnPage(wrapper, 1);
        checkTextareas.checkPropsTextarea(textareas[0], 'Описание вопроса:', wrapper.vm.quizItemDescription);
        checkTextareas.checkTextareaWhenThereIsNoRequest(textareas[0], wrapper.vm.quizItemDescription, 'Текст вопроса');
        
        await checkBaseModal.hideBaseModal(wrapper, hideModal);
        await checkBaseModal.submitRequestInBaseModal(wrapper, router.post);
    });
    
    it("Монтирование компоненты AddQuizItemModal (isRequest: true)", async () => {
        app.isRequest = true;
        const wrapper = getWrapper();
        
        checkContent (wrapper);
        
        const textareas = checkTextareas.findNumberOfTextareasOnPage(wrapper, 1);
        checkTextareas.checkPropsTextarea(textareas[0], 'Описание вопроса:', wrapper.vm.quizItemDescription);
        checkTextareas.checkTextareaWhenRequestIsMade(textareas[0], wrapper.vm.quizItemDescription, 'Текст вопроса');
        
        await checkBaseModal.notHideBaseModal(wrapper, hideModal);
        await checkBaseModal.notSubmitRequestInBaseModal(wrapper, router.post);
    });
    
    it("Функция handlerAddQuizItem вызывает router.post с нужными параметрами", () => {
        const wrapper = getWrapper();
        
        const options = {
            onBefore: expect.anything(),
            onSuccess: expect.anything(),
            onError: expect.anything(),
            onFinish: expect.anything()
        };

        wrapper.vm.handlerAddQuizItem(eventCurrentTargetClassListContainsFalse);
        
        expect(router.post).toHaveBeenCalledTimes(1);
        expect(router.post).toHaveBeenCalledWith('/admin/quiz_items', {
                quiz_id: wrapper.vm.quiz.id,
                description: wrapper.vm.quizItemDescription
            }, options);
    });
    
    it("Проверка функции onSuccess", async () => {
        const wrapper = getWrapper();
        
        expect(hideModal).not.toHaveBeenCalled();
        wrapper.vm.onSuccess();
        
        expect(hideModal).toHaveBeenCalledTimes(1);
    });
});
