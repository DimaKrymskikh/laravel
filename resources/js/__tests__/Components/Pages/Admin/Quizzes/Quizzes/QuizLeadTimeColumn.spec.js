import { mount } from "@vue/test-utils";
import { router } from '@inertiajs/vue3';

import { updateQuiz } from '@/Services/Content/Quizzes/quizzes';
import QuizLeadTimeColumn from '@/Components/Pages/Admin/Quizzes/Quizzes/QuizLeadTimeColumn.vue';
import SimpleInput from '@/Components/Elements/Form/Input/SimpleInput.vue';
import CrossSvg from '@/Components/Svg/CrossSvg.vue';
import PencilSvg from '@/Components/Svg/PencilSvg.vue';

import { quizzes } from '@/__tests__/data/quizzes/quizzes';

vi.mock('@inertiajs/vue3');

const getWrapper = function() {
    return mount(QuizLeadTimeColumn, {
                    props: {
                        quiz: quizzes[0]
                    }
                });
};

describe("@/Components/Pages/Admin/Quizzes/Quizzes/QuizLeadTimeColumn.vue", () => {
    beforeEach(() => {
        updateQuiz.isShow = false;
    });
    
    it("Отрисовка блока QuizLeadTimeColumn с текстом (updateQuizLeadTime.isShow = false)", () => {
        const wrapper = getWrapper();
        
        const tds = wrapper.findAll('td');
        expect(tds.length).toBe(2);
        expect(tds[0].text()).toBe(wrapper.vm.quiz.lead_time + ' минут');
        expect(tds[1].findComponent(PencilSvg).exists()).toBe(true);
    });
    
    it("Отрисовка блока QuizLeadTimeColumn с input (updateQuizLeadTime.isShow = true)", () => {
        updateQuiz.isShow = true;
        
        const wrapper = getWrapper();
        
        const tds = wrapper.findAll('td');
        expect(tds.length).toBe(2);
        
        const simpleInput = tds[0].findComponent(SimpleInput);
        expect(simpleInput.exists()).toBe(true);
        expect(simpleInput.props('handler')).toBe(wrapper.vm.handlerUpdateQuiz);
        expect(simpleInput.props('errorsMessage')).toBe(wrapper.vm.updateQuizLeadTime.errorsMessage);
        
        const сrossSvg = tds[1].findComponent(CrossSvg);
        expect(сrossSvg.exists()).toBe(true);
    });
    
    it("Проверка v-model", () => {
        updateQuiz.isShow = true;
        
        const wrapper = getWrapper();
        const simpleInput = wrapper.findComponent(SimpleInput);
    
        // Поле ввода заполняется
        const input = simpleInput.get('input');
        expect(input.element.value).toBe(wrapper.vm.quiz.lead_time);
        
        input.setValue('777');
        expect(simpleInput.emitted()).toHaveProperty('update:modelValue');
        expect(simpleInput.emitted('update:modelValue')[0][0]).toBe('777');
    });
    
    it("Поле input закрывается", async () => {
        updateQuiz.isShow = true;
        
        const wrapper = getWrapper();
        // Поле input открыто
        const simpleInput = wrapper.findComponent(SimpleInput);
        expect(simpleInput.exists()).toBe(true);
        
        const tds = wrapper.findAll('td');
        expect(tds.length).toBe(2);
        
        // Крестик во второй клетке
        const cross = tds[1];
        await cross.trigger('click');
        // Поле input закрыто
        expect(simpleInput.exists()).toBe(false);
    });
    
    it("Вызов функции handlerUpdateQuiz", () => {
        const wrapper = getWrapper();
        wrapper.vm.handlerUpdateQuiz();
        
        expect(router.put).toHaveBeenCalledTimes(1);
        expect(router.put).toHaveBeenCalledWith(`/admin/quizzes/${wrapper.vm.quiz.id}`, {
            field: 'lead_time',
            value: wrapper.vm.fieldValue
        }, {
            preserveScroll: true,
            onBefore: expect.anything(),
            onSuccess: expect.anything(),
            onError: expect.anything(),
            onFinish: expect.anything()
        });
    });
});
