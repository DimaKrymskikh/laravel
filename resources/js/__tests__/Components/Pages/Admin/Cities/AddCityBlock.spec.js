import { mount } from "@vue/test-utils";
import { setActivePinia, createPinia } from 'pinia';

import PrimaryButton from '@/Components/Buttons/Variants/PrimaryButton.vue';
import AddCityModal from '@/Components/Modal/Request/Cities/AddCityModal.vue';
import AddCityBlock from '@/Components/Pages/Admin/Cities/AddCityBlock.vue';
import { useAppStore } from '@/Stores/app';

describe("@/Pages/Admin/Cities/AddCityBlock.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    
    it("Отрисовка блока AddCityBlock", async () => {
        const app = useAppStore();
        
        const wrapper = mount(AddCityBlock, {
            global: {
                provide: { app }
            }
        });
        
        // Присутствует кнопка PrimaryButton
        const primaryButton = wrapper.getComponent(PrimaryButton);
        expect(primaryButton.props('buttonText')).toBe('Добавить город');
        expect(primaryButton.props('handler')).toBe(wrapper.vm.showAddCityModal);
        
        // Модальное окно отсутствует
        expect(wrapper.findComponent(AddCityModal).exists()).toBe(false);
        // Клик по кнопке PrimaryButton открывает модальное окно
        await primaryButton.trigger('click');
        const addCityModal = wrapper.getComponent(AddCityModal);
        expect(addCityModal.props('hideAddCityModal')).toBe(wrapper.vm.hideAddCityModal);
        
        // Клик по кнопке 'Нет' закрывает модальное окно
        const modalNo = addCityModal.get('#modal-no');
        await modalNo.trigger('click');
        expect(wrapper.findComponent(AddCityModal).exists()).toBe(false);
    });
});
