import { mount, flushPromises } from "@vue/test-utils";

import QuizAnswerPriorityColumn from '@/Components/Pages/Admin/Quizzes/QuizItemCard/QuizAnswerPriorityColumn.vue';
import SimpleInput from '@/Components/Elements/Form/Input/SimpleInput.vue';
import CrossSvg from '@/Components/Svg/CrossSvg.vue';
import PencilSvg from '@/Components/Svg/PencilSvg.vue';

import { answerTrue } from '@/__tests__/data/quizzes/quizAnswers';

vi.mock('@inertiajs/vue3');

const getWrapper = function() {
    return mount(QuizAnswerPriorityColumn, {
                    props: {
                        answer: answerTrue
                    }
                });
};

describe("@/Components/Pages/Admin/Quizzes/QuizItemCard/QuizAnswerPriorityColumn.vue", () => {
    it("Отрисовка блока QuizAnswerPriorityColumn с текстом (isShow = false)", () => {
        const wrapper = getWrapper();
        
        const tds = wrapper.findAll('td');
        expect(tds.length).toBe(2);
        expect(tds[0].text()).toBe(wrapper.vm.answer.priority);
        expect(tds[1].findComponent(PencilSvg).exists()).toBe(true);
    });
    
    it("Отрисовка блока QuizAnswerPriorityColumn с input (isShow = true)", async () => {
        const wrapper = getWrapper();
        wrapper.vm.modal.isShow = true;
        await flushPromises();
        
        const tds = wrapper.findAll('td');
        expect(tds.length).toBe(2);
        
        const simpleInput = tds[0].findComponent(SimpleInput);
        expect(simpleInput.exists()).toBe(true);
        expect(simpleInput.props('handler')).toBe(wrapper.vm.handler);
        expect(simpleInput.props('hide')).toBe(wrapper.vm.hide);
        
        const сrossSvg = tds[1].findComponent(CrossSvg);
        expect(сrossSvg.exists()).toBe(true);
    });
    
    it("Проверка v-model", async () => {
        const wrapper = getWrapper();
        wrapper.vm.modal.show(wrapper.vm.id, wrapper.vm.field, wrapper.vm.url);
        await flushPromises();
        
        const simpleInput = wrapper.findComponent(SimpleInput);
    
        // Поле ввода заполняется
        const input = simpleInput.get('input');
        expect(input.element.value).toBe(wrapper.vm.answer.priority);
        
        const newPriority = '25';
        input.setValue(newPriority);
        expect(simpleInput.emitted()).toHaveProperty('update:modelValue');
        expect(simpleInput.emitted('update:modelValue')[0][0]).toBe(newPriority);
    });
    
    it("Клик по карадашу открывает поле input", async () => {
        const wrapper = getWrapper();
        
        const pencilSvg = wrapper.findComponent(PencilSvg);
        // Поле input закрыто
        expect(wrapper.findComponent(SimpleInput).exists()).toBe(false);
        
        await pencilSvg.trigger('click');
        // Поле input открыто
        expect(wrapper.findComponent(SimpleInput).exists()).toBe(true);
    });
    
    it("Вызов функции hide (app.isRequest = false)", async () => {
        const wrapper = getWrapper();
        wrapper.vm.modal.isShow = true;
        
        wrapper.vm.hide();
        expect(wrapper.vm.modal.isShow).toBe(false);
    });
});
