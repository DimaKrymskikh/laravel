import { mount } from "@vue/test-utils";

import { router } from '@inertiajs/vue3';

import { app } from '@/Services/app';
import { newQuizAnswer } from '@/Services/Content/Quizzes/quizItemCard';
import AddQuizAnswerModal from '@/Components/Pages/Admin/Quizzes/QuizItemCard/AddQuizAnswerModal.vue';
import Checkbox from '@/components/Elements/Form/Checkbox.vue';

import { quizItemWithoutAnswers } from '@/__tests__/data/quizzes/quizItems';
import { checkBaseModal } from '@/__tests__/methods/checkBaseModal';
import { checkTextareas } from '@/__tests__/methods/checkTextareas';
import { eventCurrentTargetClassListContainsFalse } from '@/__tests__/fake/Event';
import * as testCheckbox from '@/__tests__/methods/Checkbox/checkbox';

vi.mock('@inertiajs/vue3');
        
const hideModal = vi.spyOn(newQuizAnswer, 'hide');

const getWrapper = function() {
    return mount(AddQuizAnswerModal, {
        props: {
            quizItem: quizItemWithoutAnswers
        }
    });
};

const checkContent = function(wrapper) {
    // Проверка равенства переменных ref начальным данным
    expect(wrapper.vm.quizAnswerDescription).toBe(newQuizAnswer.description);
    expect(wrapper.vm.quizAnswerIsCorrect).toBe(newQuizAnswer.isCorrect);

    // Заголовок модального окна задаётся
    expect(wrapper.text()).toContain('Добавление ответа');
};
        
describe("@/Components/Pages/Admin/Quizzes/QuizItemCard/AddQuizAnswerModal.vue", () => {
    beforeEach(() => {
        app.isRequest = false;
    });
    
    it("Монтирование компоненты AddQuizAnswerModal (isRequest: false)", async () => {
        const wrapper = getWrapper();
        
        checkContent (wrapper);
        
        const textareas = checkTextareas.findNumberOfTextareasOnPage(wrapper, 1);
        checkTextareas.checkPropsTextarea(textareas[0], 'Текст ответа:', wrapper.vm.quizAnswerDescription);
        checkTextareas.checkTextareaWhenThereIsNoRequest(textareas[0], wrapper.vm.quizAnswerDescription, 'Текст ответа');
        
        const checkbox = wrapper.findComponent(Checkbox);
        expect(checkbox.props('titleText')).toBe("Ответ правильный:");
        testCheckbox.successChecked(checkbox);
        
        await checkBaseModal.hideBaseModal(wrapper, hideModal);
        await checkBaseModal.submitRequestInBaseModal(wrapper, router.post);
    });
    
    it("Монтирование компоненты AddQuizAnswerModal (isRequest: true)", async () => {
        app.isRequest = true;
        const wrapper = getWrapper();
        
        checkContent (wrapper);
        
        const textareas = checkTextareas.findNumberOfTextareasOnPage(wrapper, 1);
        checkTextareas.checkPropsTextarea(textareas[0], 'Текст ответа:', wrapper.vm.quizAnswerDescription);
        checkTextareas.checkTextareaWhenRequestIsMade(textareas[0], wrapper.vm.quizAnswerDescription, 'Текст вопроса');
        
        const checkbox = wrapper.findComponent(Checkbox);
        expect(checkbox.props('titleText')).toBe("Ответ правильный:");
        testCheckbox.failChecked(checkbox);
        
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

        wrapper.vm.handlerAddQuizAnswer(eventCurrentTargetClassListContainsFalse);
        
        expect(router.post).toHaveBeenCalledTimes(1);
        expect(router.post).toHaveBeenCalledWith('/admin/quiz_answers', {
                quiz_item_id: wrapper.vm.quizItem.id,
                description: wrapper.vm.quizAnswerDescription,
                is_correct: wrapper.vm.quizAnswerIsCorrect
            }, options);
    });
    
    it("Проверка функции onSuccess", async () => {
        const wrapper = getWrapper();
        
        expect(hideModal).not.toHaveBeenCalled();
        wrapper.vm.onSuccess();
        
        expect(hideModal).toHaveBeenCalledTimes(1);
    });
});
