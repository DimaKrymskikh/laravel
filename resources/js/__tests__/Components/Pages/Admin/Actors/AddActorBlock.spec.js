import { mount } from "@vue/test-utils";
import { setActivePinia, createPinia } from 'pinia';

import { actor } from '@/Services/Content/actors';
import PrimaryButton from '@/Components/Buttons/Variants/PrimaryButton.vue';
import AddActorModal from '@/Components/Modal/Request/Actors/AddActorModal.vue';
import AddActorBlock from '@/Components/Pages/Admin/Actors/AddActorBlock.vue';
import { useActorsListStore } from '@/Stores/actors';

describe("@/Components/Pages/Admin/Actors/AddActorBlock.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    
    it("Отрисовка блока AddActorBlock", async () => {
        const actorsList = useActorsListStore();
        
        const wrapper = mount(AddActorBlock, {
            global: {
                provide: { actorsList }
            }
        });
        
        // Присутствует кнопка PrimaryButton
        const primaryButton = wrapper.getComponent(PrimaryButton);
        expect(primaryButton.props('buttonText')).toBe('Добавить актёра');
        expect(primaryButton.props('handler')).toBe(wrapper.vm.showModal);
        
        // Модальное окно отсутствует
        expect(wrapper.findComponent(AddActorModal).exists()).toBe(false);
        // Клик по кнопке PrimaryButton открывает модальное окно
        await primaryButton.trigger('click');
        const addActorModal = wrapper.getComponent(AddActorModal);
        expect(addActorModal.exists()).toBe(true);
        
        // Клик по кнопке 'Нет' закрывает модальное окно
        const modalNo = addActorModal.get('#modal-no');
        await modalNo.trigger('click');
        expect(wrapper.findComponent(AddActorModal).exists()).toBe(false);
    });
});
