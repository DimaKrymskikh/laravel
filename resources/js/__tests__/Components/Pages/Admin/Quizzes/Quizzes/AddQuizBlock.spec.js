import { mount } from "@vue/test-utils";

import PrimaryButton from '@/Components/Buttons/Variants/PrimaryButton.vue';
import AddQuizBlock from '@/Components/Pages/Admin/Quizzes/Quizzes/AddQuizBlock.vue';
import AddQuizModal from '@/Components/Pages/Admin/Quizzes/Quizzes/AddQuizModal.vue';

describe("@/Components/Pages/Admin/Quizzes/Quizzes/AddQuizBlock.vue", () => {
    it("Отрисовка блока AddQuizBlock", async () => {
        const wrapper = mount(AddQuizBlock);
        
        // Присутствует кнопка PrimaryButton
        const primaryButton = wrapper.getComponent(PrimaryButton);
        expect(primaryButton.props('buttonText')).toBe('Добавить опрос');
        expect(primaryButton.props('handler')).toBe(wrapper.vm.showModal);
        
        // Модальное окно отсутствует
        expect(wrapper.findComponent(AddQuizModal).exists()).toBe(false);
        // Клик по кнопке PrimaryButton открывает модальное окно
        await primaryButton.trigger('click');
        const addQuizModal = wrapper.getComponent(AddQuizModal);
        expect(addQuizModal.exists()).toBe(true);
    });
});
