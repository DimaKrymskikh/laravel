import { mount } from "@vue/test-utils";
import { router } from '@inertiajs/vue3';

import { updateQuiz } from '@/Services/Content/Quizzes/quizzes';
import QuizDescriptionColumn from '@/Components/Pages/Admin/Quizzes/Quizzes/QuizDescriptionColumn.vue';
import SimpleTextarea from '@/Components/Elements/Form/Textarea/SimpleTextarea.vue';
import CrossSvg from '@/Components/Svg/CrossSvg.vue';
import PencilSvg from '@/Components/Svg/PencilSvg.vue';

import { quizzes } from '@/__tests__/data/quizzes/quizzes';

vi.mock('@inertiajs/vue3');

const getWrapper = function() {
    return mount(QuizDescriptionColumn, {
                    props: {
                        quiz: quizzes[0]
                    }
                });
};

describe("@/Components/Pages/Admin/Quizzes/Quizzes/QuizDescriptionColumn.vue", () => {
    beforeEach(() => {
        updateQuiz.isShow = false;
    });
    
    it("Отрисовка блока QuizDescriptionColumn с текстом (updateQuizDescription.isShow = false)", () => {
        const wrapper = getWrapper();
        
        const tds = wrapper.findAll('td');
        expect(tds.length).toBe(2);
        expect(tds[0].text()).toBe(wrapper.vm.quiz.description);
        expect(tds[1].findComponent(PencilSvg).exists()).toBe(true);
    });
    
    it("Отрисовка блока QuizDescriptionColumn с input (updateQuizDescription.isShow = true)", () => {
        updateQuiz.isShow = true;
        
        const wrapper = getWrapper();
        
        const tds = wrapper.findAll('td');
        expect(tds.length).toBe(2);
        
        const simpleTextarea = tds[0].findComponent(SimpleTextarea);
        expect(simpleTextarea.exists()).toBe(true);
        expect(simpleTextarea.props('handler')).toBe(wrapper.vm.handlerUpdateQuiz);
        expect(simpleTextarea.props('errorsMessage')).toBe(wrapper.vm.updateQuizDescription.errorsMessage);
        
        const сrossSvg = tds[1].findComponent(CrossSvg);
        expect(сrossSvg.exists()).toBe(true);
    });
    
    it("Проверка v-model", () => {
        updateQuiz.isShow = true;
        
        const wrapper = getWrapper();
        const simpleTextarea = wrapper.findComponent(SimpleTextarea);
    
        // Поле ввода заполняется
        const textarea = simpleTextarea.get('textarea');
        expect(textarea.element.value).toBe(wrapper.vm.quiz.description);
        
        textarea.setValue('Test Quiz Description');
        expect(simpleTextarea.emitted()).toHaveProperty('update:modelValue');
        expect(simpleTextarea.emitted('update:modelValue')[0][0]).toBe('Test Quiz Description');
    });
    
    it("Поле input закрывается", async () => {
        updateQuiz.isShow = true;
        
        const wrapper = getWrapper();
        // Поле input открыто
        const simpleTextarea = wrapper.findComponent(SimpleTextarea);
        expect(simpleTextarea.exists()).toBe(true);
        
        const tds = wrapper.findAll('td');
        expect(tds.length).toBe(2);
        
        // Крестик во второй клетке
        const cross = tds[1];
        await cross.trigger('click');
        // Поле input закрыто
        expect(simpleTextarea.exists()).toBe(false);
    });
    
    it("Вызов функции handlerUpdateQuiz", () => {
        const wrapper = getWrapper();
        wrapper.vm.handlerUpdateQuiz();
        
        expect(router.put).toHaveBeenCalledTimes(1);
        expect(router.put).toHaveBeenCalledWith(`/admin/quizzes/${wrapper.vm.quiz.id}`, {
            field: 'description',
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
