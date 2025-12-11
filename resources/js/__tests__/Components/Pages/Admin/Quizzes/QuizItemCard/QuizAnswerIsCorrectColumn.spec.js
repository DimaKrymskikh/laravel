import { mount, flushPromises } from "@vue/test-utils";

import QuizAnswerIsCorrectColumn from '@/Components/Pages/Admin/Quizzes/QuizItemCard/QuizAnswerIsCorrectColumn.vue';
import Checkbox from '@/components/Elements/Form/Checkbox.vue';
import CrossSvg from '@/Components/Svg/CrossSvg.vue';
import PencilSvg from '@/Components/Svg/PencilSvg.vue';
import CheckSvg from '@/Components/Svg/CheckSvg.vue';

import { answerTrue } from '@/__tests__/data/quizzes/quizAnswers';
import * as testCheckbox from '@/__tests__/methods/Checkbox/checkbox';

vi.mock('@inertiajs/vue3');

const getWrapper = function() {
    return mount(QuizAnswerIsCorrectColumn, {
                    props: {
                        answer: answerTrue
                    }
                });
};

describe("@/Components/Pages/Admin/Quizzes/QuizItemCard/QuizAnswerIsCorrectColumn.vue", () => {
    it("Отрисовка блока QuizAnswerDescriptionColumn с текстом (isShow = false)", () => {
        const wrapper = getWrapper();
        
        const tds = wrapper.findAll('td');
        expect(tds.length).toBe(2);
        
        const checkSvg = tds[0].findComponent(CheckSvg);
        // Ответ правильный
        expect(checkSvg.exists()).toBe(true);
        expect(tds[1].findComponent(PencilSvg).exists()).toBe(true);
    });
    
    it("Отрисовка блока QuizAnswerDescriptionColumn с input (isShow = true)", async () => {
        const wrapper = getWrapper();
        wrapper.vm.modal.isShow = true;
        await flushPromises();
        
        const tds = wrapper.findAll('td');
        expect(tds.length).toBe(2);
        
        const сheckbox = tds[0].findComponent(Checkbox);
        expect(сheckbox.exists()).toBe(true);
        
        const сrossSvg = tds[1].findComponent(CrossSvg);
        expect(сrossSvg.exists()).toBe(true);
    });
    
    it("Проверка v-model", async () => {
        const wrapper = getWrapper();
        wrapper.vm.modal.show(wrapper.vm.id, wrapper.vm.field, wrapper.vm.url);
        await flushPromises();
        
        const checkbox = wrapper.findComponent(Checkbox);
    
        const input = checkbox.get('input');
        expect(input.attributes('type')).toBe('checkbox');
        expect(input.attributes('disabled')).toBe(undefined);

        // Значение checkbox изменяется
        await input.setValue(!wrapper.vm.answer.is_correct);
        expect(checkbox.emitted()).toHaveProperty('update:modelValue');
        expect(checkbox.emitted('update:modelValue')[0][0]).toBe(!wrapper.vm.answer.is_correct);
    });
    
    it("Клик по карадашу открывает checkbox", async () => {
        const wrapper = getWrapper();
        
        const pencilSvg = wrapper.findComponent(PencilSvg);
        expect(wrapper.findComponent(Checkbox).exists()).toBe(false);
        
        await pencilSvg.trigger('click');
        expect(wrapper.findComponent(Checkbox).exists()).toBe(true);
    });
    
    it("Вызов функции hide (app.isRequest = false)", async () => {
        const wrapper = getWrapper();
        wrapper.vm.modal.isShow = true;
        
        wrapper.vm.hide();
        expect(wrapper.vm.modal.isShow).toBe(false);
    });
});
