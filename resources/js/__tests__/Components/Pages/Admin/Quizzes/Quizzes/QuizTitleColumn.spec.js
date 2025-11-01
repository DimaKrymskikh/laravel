import { mount } from "@vue/test-utils";
import { router } from '@inertiajs/vue3';

import { updateQuiz } from '@/Services/Content/Quizzes/quizzes';
import QuizTitleColumn from '@/Components/Pages/Admin/Quizzes/Quizzes/QuizTitleColumn.vue';
import SimpleInput from '@/Components/Elements/Form/Input/SimpleInput.vue';
import CrossSvg from '@/Components/Svg/CrossSvg.vue';
import PencilSvg from '@/Components/Svg/PencilSvg.vue';

import { quizzes } from '@/__tests__/data/quizzes/quizzes';

vi.mock('@inertiajs/vue3');

const getWrapper = function() {
    return mount(QuizTitleColumn, {
                    props: {
                        quiz: quizzes[0]
                    }
                });
};

describe("@/Components/Pages/Admin/Quizzes/Quizzes/QuizTitleColumn.vue", () => {
    beforeEach(() => {
        updateQuiz.isShow = false;
    });
    
    it("Отрисовка блока QuizTitleColumn с текстом (updateQuizTitle.isShow = false)", () => {
        const wrapper = getWrapper();
        
        const tds = wrapper.findAll('td');
        expect(tds.length).toBe(2);
        expect(tds[0].text()).toBe(wrapper.vm.quiz.title);
        expect(tds[1].findComponent(PencilSvg).exists()).toBe(true);
    });
    
    it("Отрисовка блока QuizTitleColumn с input (updateQuizTitle.isShow = true)", () => {
        updateQuiz.isShow = true;
        
        const wrapper = getWrapper();
        
        const tds = wrapper.findAll('td');
        expect(tds.length).toBe(2);
        
        const simpleInput = tds[0].findComponent(SimpleInput);
        expect(simpleInput.exists()).toBe(true);
        expect(simpleInput.props('handler')).toBe(wrapper.vm.handlerUpdateQuiz);
        expect(simpleInput.props('errorsMessage')).toBe(wrapper.vm.updateQuizTitle.errorsMessage);
        
        const сrossSvg = tds[1].findComponent(CrossSvg);
        expect(сrossSvg.exists()).toBe(true);
    });
    
    it("Проверка v-model", () => {
        updateQuiz.isShow = true;
        
        const wrapper = getWrapper();
        const simpleInput = wrapper.findComponent(SimpleInput);
    
        // Поле ввода заполняется
        const input = simpleInput.get('input');
        expect(input.element.value).toBe(wrapper.vm.quiz.title);
        
        input.setValue('Test Quiz Title');
        expect(simpleInput.emitted()).toHaveProperty('update:modelValue');
        expect(simpleInput.emitted('update:modelValue')[0][0]).toBe('Test Quiz Title');
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
            field: 'title',
            value: wrapper.vm.fieldValue
        }, {
            preserveScroll: true,
            onBefore: expect.anything(),
            onSuccess: expect.anything(),
            onError: expect.anything(),
            onFinish: expect.anything()
        });
    });
    
    it("Клик по PencilSvg открывает SimpleInput", async () => {
        const wrapper = getWrapper();
        
        const pencilSvg = wrapper.findComponent(PencilSvg);
        
        await pencilSvg.trigger('click');
        expect(wrapper.findComponent(SimpleInput).exists()).toBe(true);
    });
});
