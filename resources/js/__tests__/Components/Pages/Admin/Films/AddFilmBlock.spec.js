import { mount } from "@vue/test-utils";
import { setActivePinia, createPinia } from 'pinia';

import { film } from '@/Services/Content/films';
import AddFilmBlock from '@/Components/Pages/Admin/Films/AddFilmBlock.vue';
import PrimaryButton from '@/Components/Buttons/Variants/PrimaryButton.vue';
import AddFilmModal from '@/Components/Modal/Request/Films/AddFilmModal.vue';
import { useFilmsAdminStore } from '@/Stores/films';

describe("@/Components/Pages/Admin/Cities/AddFilmBlock.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    
    it("Отрисовка блока AddFilmBlock", async () => {
        const filmsAdmin = useFilmsAdminStore();
        
        const wrapper = mount(AddFilmBlock, {
            global: {
                provide: { filmsAdmin }
            }
        });
        
        // Присутствует кнопка PrimaryButton
        const primaryButton = wrapper.getComponent(PrimaryButton);
        expect(primaryButton.props('buttonText')).toBe('Добавить фильм');
        expect(primaryButton.props('handler')).toBe(wrapper.vm.showModal);
        
        // Модальное окно отсутствует
        expect(wrapper.findComponent(AddFilmModal).exists()).toBe(false);
        // Клик по кнопке PrimaryButton открывает модальное окно
        await primaryButton.trigger('click');
        const addFilmModal = wrapper.getComponent(AddFilmModal);
        expect(addFilmModal.exists()).toBe(true);
        
        // Клик по кнопке 'Нет' закрывает модальное окно
        const modalNo = addFilmModal.get('#modal-no');
        await modalNo.trigger('click');
        expect(wrapper.findComponent(AddFilmModal).exists()).toBe(false);
    });
});
