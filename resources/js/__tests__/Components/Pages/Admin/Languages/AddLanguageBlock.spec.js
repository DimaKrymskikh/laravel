import { mount } from "@vue/test-utils";
import { setActivePinia, createPinia } from 'pinia';

import PrimaryButton from '@/Components/Buttons/Variants/PrimaryButton.vue';
import AddLanguageBlock from '@/Components/Pages/Admin/Languages/AddLanguageBlock.vue';
import AddLanguageModal from '@/Components/Modal/Request/Languages/AddLanguageModal.vue';

describe("@/Pages/Admin/Cities/AddCityBlock.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    
    it("Отрисовка блока AddLanguageBlock", async () => {
        const wrapper = mount(AddLanguageBlock);
        
        // Присутствует кнопка PrimaryButton
        const primaryButton = wrapper.getComponent(PrimaryButton);
        expect(primaryButton.props('buttonText')).toBe('Добавить язык');
        expect(primaryButton.props('handler')).toBe(wrapper.vm.showModal);
        
        // Модальное окно отсутствует
        expect(wrapper.findComponent(AddLanguageModal).exists()).toBe(false);
        // Клик по кнопке PrimaryButton открывает модальное окно
        await primaryButton.trigger('click');
        const addLanguageModal = wrapper.getComponent(AddLanguageModal);
        expect(addLanguageModal.props('hideAddLanguageModal')).toBe(wrapper.vm.hideAddLanguageModal);
        
        // Клик по кнопке 'Нет' закрывает модальное окно
        const modalNo = addLanguageModal.get('#modal-no');
        await modalNo.trigger('click');
        expect(wrapper.findComponent(AddLanguageModal).exists()).toBe(false);
    });
});
