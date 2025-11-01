import { mount } from "@vue/test-utils";

import { router } from '@inertiajs/vue3';

import { app } from '@/Services/app';
import { newQuiz } from '@/Services/Content/Quizzes/quizzes';
import AddQuizModal from '@/Components/Pages/Admin/Quizzes/Quizzes/AddQuizModal.vue';

import { checkBaseModal } from '@/__tests__/methods/checkBaseModal';
import { checkInputField } from '@/__tests__/methods/checkInputField';
import { checkTextareas } from '@/__tests__/methods/checkTextareas';
import { eventCurrentTargetClassListContainsFalse } from '@/__tests__/fake/Event';

vi.mock('@inertiajs/vue3');
        
const hideModal = vi.spyOn(newQuiz, 'hide');

const getWrapper = function() {
    return mount(AddQuizModal);
};

const checkContent = function(wrapper) {
    // Проверка равенства переменных ref начальным данным
    expect(wrapper.vm.quizTitle).toBe(newQuiz.title);
    expect(wrapper.vm.quizDescription).toBe(newQuiz.description);
    expect(wrapper.vm.quizLeadTime).toBe(newQuiz.leadTime);
    expect(wrapper.vm.errorsTitle).toBe('');
    expect(wrapper.vm.errorsLeadTime).toBe('');

    // Заголовок модального окна задаётся
    expect(wrapper.text()).toContain('Добавление опроса');
};
        
describe("@/Components/Pages/Admin/Quizzes/Quizzes/AddQuizModal.vue", () => {
    it("Монтирование компоненты AddQuizModal (isRequest: false)", async () => {
        const wrapper = getWrapper();
        
        checkContent (wrapper);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 2);
        checkInputField.checkPropsInputField(inputFields[0], 'Название опроса:', 'text', wrapper.vm.errorsTitle, wrapper.vm.quizTitle, true);
        checkInputField.checkPropsInputField(inputFields[1], 'Продолжительность опроса (в минутах):', 'text', wrapper.vm.errorsLeadTime, wrapper.vm.quizLeadTime);
        
        checkInputField.checkInputFieldWhenThereIsNoRequest(inputFields[0], wrapper.vm.quizTitle, 'Название опроса');
        checkInputField.checkInputFieldWhenThereIsNoRequest(inputFields[1], wrapper.vm.quizLeadTime, '35');
        
        const textareas = checkTextareas.findNumberOfTextareasOnPage(wrapper, 1);
        checkTextareas.checkPropsTextarea(textareas[0], 'Описание опроса:', wrapper.vm.quizDescription);
        checkTextareas.checkTextareaWhenThereIsNoRequest(textareas[0], wrapper.vm.quizDescription, 'Описание опроса');
        
        await checkBaseModal.hideBaseModal(wrapper, hideModal);
        await checkBaseModal.submitRequestInBaseModal(wrapper, router.post);
    });
    
    it("Монтирование компоненты AddQuizModal (isRequest: true)", async () => {
        app.isRequest = true;
        const wrapper = getWrapper();
        
        checkContent (wrapper);
        
        const inputFields = checkInputField.findNumberOfInputFieldOnPage(wrapper, 2);
        checkInputField.checkPropsInputField(inputFields[0], 'Название опроса:', 'text', wrapper.vm.errorsTitle, wrapper.vm.quizTitle, true);
        checkInputField.checkPropsInputField(inputFields[1], 'Продолжительность опроса (в минутах):', 'text', wrapper.vm.errorsLeadTime, wrapper.vm.quizLeadTime);
        
        checkInputField.checkInputFieldWhenRequestIsMade(inputFields[0], wrapper.vm.quizTitle, 'Название опроса');
        checkInputField.checkInputFieldWhenRequestIsMade(inputFields[1], wrapper.vm.quizLeadTime, '35');
        
        const textareas = checkTextareas.findNumberOfTextareasOnPage(wrapper, 1);
        checkTextareas.checkPropsTextarea(textareas[0], 'Описание опроса:', wrapper.vm.quizDescription);
        checkTextareas.checkTextareaWhenRequestIsMade(textareas[0], wrapper.vm.quizDescription, 'Описание опроса');
        
        await checkBaseModal.notHideBaseModal(wrapper, hideModal);
        await checkBaseModal.notSubmitRequestInBaseModal(wrapper, router.post);
    });
    
    it("Функция handlerAddQuiz вызывает router.post с нужными параметрами", () => {
        const wrapper = getWrapper();
        
        const options = {
            onBefore: expect.anything(),
            onSuccess: expect.anything(),
            onError: expect.anything(),
            onFinish: expect.anything()
        };

        wrapper.vm.handlerAddQuiz(eventCurrentTargetClassListContainsFalse);
        
        expect(router.post).toHaveBeenCalledTimes(1);
        expect(router.post).toHaveBeenCalledWith('/admin/quizzes', {
                title: wrapper.vm.quizTitle,
                description: wrapper.vm.quizDescription,
                lead_time: wrapper.vm.quizLeadTime
            }, options);
    });
    
    it("Проверка функции onBefore", () => {
        const wrapper = getWrapper();
        
        wrapper.vm.errorsTitle = 'ErrorTitle';
        wrapper.vm.errorsLeadTime = 'ErrorLeadTime';
        wrapper.vm.onBefore();
        
        expect(app.isRequest).toBe(true);
        expect(wrapper.vm.errorsTitle).toBe('');
        expect(wrapper.vm.errorsLeadTime).toBe('');
    });
    
    it("Проверка функции onSuccess", async () => {
        const wrapper = getWrapper();
        
        expect(hideModal).not.toHaveBeenCalled();
        wrapper.vm.onSuccess();
        
        expect(hideModal).toHaveBeenCalledTimes(1);
        expect(hideModal).toHaveBeenCalledWith();
    });
    
    it("Проверка функции onError", async () => {
        const wrapper = getWrapper();
        
        expect(wrapper.vm.errorsTitle).toBe('');
        expect(wrapper.vm.errorsLeadTime).toBe('');
        wrapper.vm.onError({ title: 'ErrorTitle', lead_time: 'ErrorLeadTime' });
        
        expect(wrapper.vm.errorsTitle).toBe('ErrorTitle');
        expect(wrapper.vm.errorsLeadTime).toBe('ErrorLeadTime');
    });
});
