import { mount } from "@vue/test-utils";

import { quizAnswer } from '@/Services/Content/Quizzes/quizItemCard';
import PrimaryButton from '@/Components/Buttons/Variants/PrimaryButton.vue';
import AddQuizAnswerBlock from '@/Components/Pages/Admin/Quizzes/QuizItemCard/AddQuizAnswerBlock.vue';
import AddQuizAnswerModal from '@/Components/Pages/Admin/Quizzes/QuizItemCard/AddQuizAnswerModal.vue';

describe("@/Components/Pages/Admin/Quizzes/QuizItemCard/AddQuizAnswerBlock.vue", () => {
    it("Отрисовка блока AddQuizAnswerBlock", async () => {
        const wrapper = mount(AddQuizAnswerBlock);
        
        // Присутствует кнопка PrimaryButton
        const primaryButton = wrapper.getComponent(PrimaryButton);
        expect(primaryButton.props('buttonText')).toBe('Добавить ответ');
        expect(primaryButton.props('handler')).toBe(wrapper.vm.showModal);
        
        // Модальное окно отсутствует
        expect(wrapper.findComponent(AddQuizAnswerModal).exists()).toBe(false);
        // Клик по кнопке PrimaryButton открывает модальное окно
        await primaryButton.trigger('click');
        const addQuizModal = wrapper.getComponent(AddQuizAnswerModal);
        expect(addQuizModal.exists()).toBe(true);
    });
});
