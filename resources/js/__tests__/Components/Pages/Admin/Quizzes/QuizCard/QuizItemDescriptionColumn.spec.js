import { mount, flushPromises } from "@vue/test-utils";
import { router } from '@inertiajs/vue3';

import { app } from '@/Services/app';
import QuizItemDescriptionColumn from '@/Components/Pages/Admin/Quizzes/QuizCard/QuizItemDescriptionColumn.vue';
import SimpleTextarea from '@/Components/Elements/Form/Textarea/SimpleTextarea.vue';
import CrossSvg from '@/Components/Svg/CrossSvg.vue';
import PencilSvg from '@/Components/Svg/PencilSvg.vue';

import { quizItems } from '@/__tests__/data/quizzes/quizItems';

vi.mock('@inertiajs/vue3');

const getWrapper = function() {
    return mount(QuizItemDescriptionColumn, {
                    props: {
                        quizItem: quizItems[0]
                    }
                });
};

describe("@/Components/Pages/Admin/Quizzes/QuizCard/QuizItemDescriptionColumn.vue", () => {
    beforeEach(() => {
        app.isRequest = false;
    });
    
    it("Отрисовка блока QuizItemDescriptionColumn с текстом (isShow = false)", () => {
        const wrapper = getWrapper();
        
        const tds = wrapper.findAll('td');
        expect(tds.length).toBe(2);
        expect(tds[0].text()).toBe(wrapper.vm.quizItem.description);
        expect(tds[1].findComponent(PencilSvg).exists()).toBe(true);
    });
    
    it("Отрисовка блока QuizItemDescriptionColumn с input (updateQuizDescription.isShow = true)", async () => {
        const wrapper = getWrapper();
        wrapper.vm.isShow = true;
        await flushPromises();
        
        const tds = wrapper.findAll('td');
        expect(tds.length).toBe(2);
        
        const simpleTextarea = tds[0].findComponent(SimpleTextarea);
        expect(simpleTextarea.exists()).toBe(true);
        expect(simpleTextarea.props('handler')).toBe(wrapper.vm.updateDescription);
        
        const сrossSvg = tds[1].findComponent(CrossSvg);
        expect(сrossSvg.exists()).toBe(true);
    });
    
    it("Проверка v-model", async () => {
        const wrapper = getWrapper();
        wrapper.vm.isShow = true;
        await flushPromises();
        
        const simpleTextarea = wrapper.findComponent(SimpleTextarea);
    
        // Поле ввода заполняется
        const textarea = simpleTextarea.get('textarea');
        expect(textarea.element.value).toBe(wrapper.vm.quizItem.description);
        
        textarea.setValue('Test Quiz Description');
        expect(simpleTextarea.emitted()).toHaveProperty('update:modelValue');
        expect(simpleTextarea.emitted('update:modelValue')[0][0]).toBe('Test Quiz Description');
    });
    
    it("Клик по карадашу открывает полн textarea", async () => {
        const wrapper = getWrapper();
        
        const pencilSvg = wrapper.findComponent(PencilSvg);
        // Поле textarea закрыто
        expect(wrapper.findComponent(SimpleTextarea).exists()).toBe(false);
        
        await pencilSvg.trigger('click');
        // Поле textarea открыто
        expect(wrapper.findComponent(SimpleTextarea).exists()).toBe(true);
    });
    
    it("Поле textarea закрывается кликом по крестику", async () => {
        const wrapper = getWrapper();
        wrapper.vm.isShow = true;
        await flushPromises();
        
        // Поле textarea открыто
        expect(wrapper.findComponent(SimpleTextarea).exists()).toBe(true);
        
        const tds = wrapper.findAll('td');
        expect(tds.length).toBe(2);
        
        // Крестик во второй клетке
        const cross = tds[1];
        await cross.trigger('click');
        // Поле input закрыто
        expect(wrapper.findComponent(SimpleTextarea).exists()).toBe(false);
    });
    
    it("Вызов функции updateDescription", () => {
        const wrapper = getWrapper();
        wrapper.vm.updateDescription();
        
        expect(router.put).toHaveBeenCalledTimes(1);
        expect(router.put).toHaveBeenCalledWith(`/admin/quiz_items/${wrapper.vm.quizItem.id}`, {
            description: wrapper.vm.fieldValue
        }, {
            preserveScroll: true,
            onBefore: expect.anything(),
            onSuccess: expect.anything(),
            onError: expect.anything(),
            onFinish: expect.anything()
        });
    });
    
    it("Вызов функции onSuccess", () => {
        const wrapper = getWrapper();
        // Открываем поле textarea
        wrapper.vm.show();
        expect(wrapper.vm.isShow).toBe(true);
        
        // Вызов функции onSuccess закрывает поле textarea
        wrapper.vm.onSuccess();
        expect(wrapper.vm.isShow).toBe(false);
    });
});
