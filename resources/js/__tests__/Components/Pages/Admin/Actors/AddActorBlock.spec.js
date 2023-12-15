import { mount } from "@vue/test-utils";
import { setActivePinia, createPinia } from 'pinia';

import PrimaryButton from '@/Components/Buttons/Variants/PrimaryButton.vue';
import AddActorModal from '@/Components/Modal/Request/Actors/AddActorModal.vue';
import AddActorBlock from '@/Components/Pages/Admin/Actors/AddActorBlock.vue';
import { useAppStore } from '@/Stores/app';
import { useActorsListStore } from '@/Stores/actors';

describe("@/Components/Pages/Admin/Actors/AddActorBlock.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    
    it("Отрисовка блока AddActorBlock", async () => {
        const app = useAppStore();
        const actorsList = useActorsListStore();
        
        const wrapper = mount(AddActorBlock, {
            global: {
                provide: { app, actorsList }
            }
        });
        
        // Присутствует кнопка PrimaryButton
        const primaryButton = wrapper.getComponent(PrimaryButton);
        expect(primaryButton.props('buttonText')).toBe('Добавить актёра');
        expect(primaryButton.props('handler')).toBe(wrapper.vm.showAddActorModal);
        
        // Модальное окно отсутствует
        expect(wrapper.findComponent(AddActorModal).exists()).toBe(false);
        // Клик по кнопке PrimaryButton открывает модальное окно
        await primaryButton.trigger('click');
        const addActorModal = wrapper.getComponent(AddActorModal);
        expect(addActorModal.props('hideAddActorModal')).toBe(wrapper.vm.hideAddActorModal);
        
        // Клик по кнопке 'Нет' закрывает модальное окно
        const modalNo = addActorModal.get('#modal-no');
        await modalNo.trigger('click');
        expect(wrapper.findComponent(AddActorModal).exists()).toBe(false);
    });
});
