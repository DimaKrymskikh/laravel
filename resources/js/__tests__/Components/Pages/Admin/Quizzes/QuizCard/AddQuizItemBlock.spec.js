import { mount } from "@vue/test-utils";

import PrimaryButton from '@/Components/Buttons/Variants/PrimaryButton.vue';
import AddQuizItemBlock from '@/Components/Pages/Admin/Quizzes/QuizCard/AddQuizItemBlock.vue';
import AddQuizItemModal from '@/Components/Pages/Admin/Quizzes/QuizCard/AddQuizItemModal.vue';

describe("@/Components/Pages/Admin/Quizzes/QuizCard/AddQuizItemBlock.vue", () => {
    it("Отрисовка блока AddQuizBlock", async () => {
        const wrapper = mount(AddQuizItemBlock);
        
        // Присутствует кнопка PrimaryButton
        const primaryButton = wrapper.getComponent(PrimaryButton);
        expect(primaryButton.props('buttonText')).toBe('Добавить вопрос');
        expect(primaryButton.props('handler')).toBe(wrapper.vm.showModal);
        
        // Модальное окно отсутствует
        expect(wrapper.findComponent(AddQuizItemModal).exists()).toBe(false);
        // Клик по кнопке PrimaryButton открывает модальное окно
        await primaryButton.trigger('click');
        const addQuizModal = wrapper.getComponent(AddQuizItemModal);
        expect(addQuizModal.exists()).toBe(true);
    });
});
