import { mount, flushPromises } from "@vue/test-utils";

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
    it("Отрисовка блока QuizTitleColumn с текстом (updateQuizTitle.isShow = false)", () => {
        const wrapper = getWrapper();
        
        const tds = wrapper.findAll('td');
        expect(tds.length).toBe(2);
        expect(tds[0].text()).toBe(wrapper.vm.quiz.title);
        expect(tds[1].findComponent(PencilSvg).exists()).toBe(true);
    });
    
    it("Отрисовка блока QuizTitleColumn с input (updateQuizTitle.isShow = true)", async () => {
        const wrapper = getWrapper();
        wrapper.vm.modal.isShow = true;
        await flushPromises();
        
        const tds = wrapper.findAll('td');
        expect(tds.length).toBe(2);
        
        const simpleInput = tds[0].findComponent(SimpleInput);
        expect(simpleInput.exists()).toBe(true);
        expect(simpleInput.props('hide')).toBe(wrapper.vm.hide);
        expect(simpleInput.props('handler')).toBe(wrapper.vm.handler);
        expect(simpleInput.props('errorsMessage')).toBe(wrapper.vm.activeField.errorsMessage);
        
        const сrossSvg = tds[1].findComponent(CrossSvg);
        expect(сrossSvg.exists()).toBe(true);
    });
    
    it("Проверка v-model", async () => {
        const wrapper = getWrapper();
        wrapper.vm.modal.show(wrapper.vm.id, wrapper.vm.field, wrapper.vm.url);
        await flushPromises();
        
        const simpleInput = wrapper.findComponent(SimpleInput);
    
        const input = simpleInput.get('input');
        expect(input.element.value).toBe(wrapper.vm.quiz.title);
        
        input.setValue('Test Quiz Title');
        expect(simpleInput.emitted()).toHaveProperty('update:modelValue');
        expect(simpleInput.emitted('update:modelValue')[0][0]).toBe('Test Quiz Title');
    });
    
    it("Клик по PencilSvg открывает SimpleInput", async () => {
        const wrapper = getWrapper();
        
        const pencilSvg = wrapper.findComponent(PencilSvg);
        
        await pencilSvg.trigger('click');
        expect(wrapper.findComponent(SimpleInput).exists()).toBe(true);
    });
    
    it("Вызов функции hide (app.isRequest = false)", async () => {
        const wrapper = getWrapper();
        wrapper.vm.modal.isShow = true;
        
        wrapper.vm.hide();
        expect(wrapper.vm.modal.isShow).toBe(false);
    });
});
