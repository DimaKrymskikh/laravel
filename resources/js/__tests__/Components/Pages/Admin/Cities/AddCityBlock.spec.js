import { mount } from "@vue/test-utils";

import PrimaryButton from '@/Components/Buttons/Variants/PrimaryButton.vue';
import AddCityModal from '@/Components/Modal/Request/Cities/AddCityModal.vue';
import AddCityBlock from '@/Components/Pages/Admin/Cities/AddCityBlock.vue';

describe("@/Components/Pages/Admin/Cities/AddCityBlock.vue", () => {
    it("Отрисовка блока AddCityBlock", async () => {
        const wrapper = mount(AddCityBlock);
        
        // Присутствует кнопка PrimaryButton
        const primaryButton = wrapper.getComponent(PrimaryButton);
        expect(primaryButton.props('buttonText')).toBe('Добавить город');
        expect(primaryButton.props('handler')).toBe(wrapper.vm.showModal);
        
        // Модальное окно отсутствует
        expect(wrapper.findComponent(AddCityModal).exists()).toBe(false);
        // Клик по кнопке PrimaryButton открывает модальное окно
        await primaryButton.trigger('click');
        const addCityModal = wrapper.getComponent(AddCityModal);
        expect(addCityModal.exists()).toBe(true);
        
        // Клик по кнопке 'Нет' закрывает модальное окно
        const modalNo = addCityModal.get('#modal-no');
        await modalNo.trigger('click');
        expect(wrapper.findComponent(AddCityModal).exists()).toBe(false);
    });
});
