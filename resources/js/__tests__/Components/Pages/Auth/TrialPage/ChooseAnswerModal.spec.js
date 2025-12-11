import { mount } from "@vue/test-utils";
import { router } from '@inertiajs/vue3';

import { app } from '@/Services/app';
import { trialQuestions } from '@/Services/Content/Trials/trials';
import ChooseAnswerModal from '@/Components/Pages/Auth/Trials/TrialPage/ChooseAnswerModal.vue';
import RadioInput from '@/Components/Elements/Form/Input/RadioInput.vue';
import RadioGroup from '@/Components/Elements/Groups/RadioGroup.vue';

import { quizItemWithAnswers } from '@/__tests__/data/quizzes/quizItems';
import { trials } from '@/__tests__/data/quizzes/trials';

vi.mock('@inertiajs/vue3');

const hideModal = vi.spyOn(trialQuestions, 'hide');

const getWrapper = function() {
    return mount(ChooseAnswerModal, {
        props: {
            // Пусть в тесте будет один вопрос
            quizItems: [quizItemWithAnswers]
        }
    });
};
        
describe("@/Components/Pages/Auth/Trials/TrialPage/ChooseAnswerModal.vue", () => {
    beforeEach(() => {
        trialQuestions.activeQuestion = trials[0].answers[0];
        app.isRequest = false;
    });
    
    it("Монтирование компоненты ChooseAnswerModal (isRequest: false)", async () => {
        const wrapper = getWrapper();
        
        const radioGroup = wrapper.findComponent(RadioGroup);
        expect(radioGroup.exists()).toBe(true);
        
        const radioInputs = radioGroup.findAllComponents(RadioInput);
        expect(radioInputs.length).toBe(quizItemWithAnswers.answers.length);
        
        // Клики по радиокнопкам изменяет выбранный ответ
        const input0 = radioInputs[0].get('input');
        await input0.trigger('click');
        expect(wrapper.vm.chosenAnswer).toBe('' + quizItemWithAnswers.answers[0].id);
        
        const input1 = radioInputs[1].get('input');
        await input1.trigger('click');
        expect(wrapper.vm.chosenAnswer).toBe('' + quizItemWithAnswers.answers[1].id);
    });
    
    it("Монтирование компоненты ChooseAnswerModal (isRequest: true)", async () => {
        app.isRequest = true;
        const wrapper = getWrapper();
        
        
        const radioGroup = wrapper.findComponent(RadioGroup);
        const radioInputs = radioGroup.findAllComponents(RadioInput);
        
        // Клики по радиокнопкам не влияет на выбранный ответ
        const input0 = radioInputs[0].get('input');
        await input0.trigger('click');
        expect(wrapper.vm.chosenAnswer).toBe(null);
        
        const input1 = radioInputs[1].get('input');
        await input1.trigger('click');
        expect(wrapper.vm.chosenAnswer).toBe(null);
    });
    
    it("Функция handlerChooseAnswer вызывает router.post с нужными параметрами", async () => {
        const wrapper = getWrapper();
        
        // Выбираем ответ (chosenAnswer)
        const radioGroup = wrapper.findComponent(RadioGroup);
        const radioInputs = radioGroup.findAllComponents(RadioInput);
        const input0 = radioInputs[0].get('input');
        await input0.trigger('click');
        
        const options = {
            onBefore: expect.anything(),
            onError: expect.anything(),
            onFinish: expect.anything()
        };

        wrapper.vm.handlerChooseAnswer();
        
        expect(router.post).toHaveBeenCalledTimes(1);
        expect(router.post).toHaveBeenCalledWith('/trials/choose_answer', {
                id: trialQuestions.activeQuestion.id,
                quiz_answer_id: wrapper.vm.chosenAnswer
            }, options);
    });
    
    it("Функция handlerChooseAnswer не вызывает router.post, если ответ не выбран", async () => {
        const wrapper = getWrapper();

        wrapper.vm.handlerChooseAnswer();
        
        expect(router.post).not.toHaveBeenCalled();
    });
    
    it("Проверка функции onFinish", async () => {
        const wrapper = getWrapper();
        
        expect(hideModal).not.toHaveBeenCalled();
        wrapper.vm.onFinish();
        
        expect(hideModal).toHaveBeenCalledTimes(1);
    });
    
    it("Проверка функции hideModal", async () => {
        trialQuestions.isShow = true;
        
        const wrapper = getWrapper();
        
        wrapper.vm.hideModal();
        
        expect(trialQuestions.isShow).toBe(false);
    });
});
