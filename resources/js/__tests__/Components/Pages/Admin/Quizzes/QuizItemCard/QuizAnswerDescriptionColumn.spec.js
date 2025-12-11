import { mount, flushPromises } from "@vue/test-utils";

import QuizAnswerDescriptionColumn from '@/Components/Pages/Admin/Quizzes/QuizItemCard/QuizAnswerDescriptionColumn.vue';
import SimpleTextarea from '@/Components/Elements/Form/Textarea/SimpleTextarea.vue';
import CrossSvg from '@/Components/Svg/CrossSvg.vue';
import PencilSvg from '@/Components/Svg/PencilSvg.vue';

import { answerTrue } from '@/__tests__/data/quizzes/quizAnswers';

vi.mock('@inertiajs/vue3');

const getWrapper = function() {
    return mount(QuizAnswerDescriptionColumn, {
                    props: {
                        answer: answerTrue
                    }
                });
};

describe("@/Components/Pages/Admin/Quizzes/QuizItemCard/QuizAnswerDescriptionColumn.vue", () => {
    it("Отрисовка блока QuizAnswerDescriptionColumn с текстом (isShow = false)", () => {
        const wrapper = getWrapper();
        
        const tds = wrapper.findAll('td');
        expect(tds.length).toBe(2);
        expect(tds[0].text()).toBe(wrapper.vm.answer.description);
        expect(tds[1].findComponent(PencilSvg).exists()).toBe(true);
    });
    
    it("Отрисовка блока QuizAnswerDescriptionColumn с input (isShow = true)", async () => {
        const wrapper = getWrapper();
        wrapper.vm.modal.isShow = true;
        await flushPromises();
        
        const tds = wrapper.findAll('td');
        expect(tds.length).toBe(2);
        
        const simpleTextarea = tds[0].findComponent(SimpleTextarea);
        expect(simpleTextarea.exists()).toBe(true);
        expect(simpleTextarea.props('handler')).toBe(wrapper.vm.handler);
        expect(simpleTextarea.props('hide')).toBe(wrapper.vm.hide);
        
        const сrossSvg = tds[1].findComponent(CrossSvg);
        expect(сrossSvg.exists()).toBe(true);
    });
    
    it("Проверка v-model", async () => {
        const wrapper = getWrapper();
        wrapper.vm.modal.show(wrapper.vm.id, wrapper.vm.field, wrapper.vm.url);
        await flushPromises();
        
        const simpleTextarea = wrapper.findComponent(SimpleTextarea);
    
        // Поле ввода заполняется
        const textarea = simpleTextarea.get('textarea');
        expect(textarea.element.value).toBe(wrapper.vm.props.answer.description);
        
        const newDescription = 'Test Quiz Answer Description';
        textarea.setValue(newDescription);
        expect(simpleTextarea.emitted()).toHaveProperty('update:modelValue');
        expect(simpleTextarea.emitted('update:modelValue')[0][0]).toBe(newDescription);
    });
    
    it("Клик по карадашу открывает поле textarea", async () => {
        const wrapper = getWrapper();
        
        const pencilSvg = wrapper.findComponent(PencilSvg);
        // Поле textarea закрыто
        expect(wrapper.findComponent(SimpleTextarea).exists()).toBe(false);
        
        await pencilSvg.trigger('click');
        // Поле textarea открыто
        expect(wrapper.findComponent(SimpleTextarea).exists()).toBe(true);
    });
    
    it("Вызов функции hide (app.isRequest = false)", async () => {
        const wrapper = getWrapper();
        wrapper.vm.modal.isShow = true;
        
        wrapper.vm.hide();
        expect(wrapper.vm.modal.isShow).toBe(false);
    });
});
