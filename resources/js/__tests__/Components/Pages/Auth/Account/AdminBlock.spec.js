import { mount } from "@vue/test-utils";

import { setActivePinia, createPinia } from 'pinia';
import AdminBlock from '@/Components/Pages/Auth/Account/AdminBlock.vue';
import AdminModal from '@/Components/Modal/Request/AdminModal.vue';
import PrimaryButton from '@/Components/Buttons/Variants/PrimaryButton.vue';
import { useAppStore } from '@/Stores/app';
import { useFilmsAccountStore } from '@/Stores/films';

describe("@/Pages/Auth/Account/AdminBlock.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    
    it("Отрисовка AdminBlock. Показ/Сокрытие AdminModal (is_admin: false)", async () => {
        const app = useAppStore();
        const filmsAccount = useFilmsAccountStore();
        
        const wrapper = mount(AdminBlock, {
            props: {
                user: {
                    id: 77,
                    is_admin: false,
                    login: 'TestLogin'
                }
            },
            global: {
                provide: { app, filmsAccount }
            }
        });
        
        const primaryButton = wrapper.getComponent(PrimaryButton);
        expect(primaryButton.props('buttonText')).toBe('Сделать себя админом');
        expect(primaryButton.props('handler')).toBe(wrapper.vm.showAdminModal);
        // В начальный момент AdminBlock скрыт
        expect(wrapper.findComponent(AdminModal).exists()).toBe(false);
        // Клик по кнопке открывает модальное окно
        await primaryButton.trigger('click');
        const adminModal = wrapper.findComponent(AdminModal);
        expect(adminModal.props('hideAdminModal')).toBe(wrapper.vm.hideAdminModal);
        expect(adminModal.props('admin')).toBe(false);
        // Клик по кнопке 'Нет' скрывает модальное окно
        const modalNo = wrapper.get('#modal-no');
        await modalNo.trigger('click');
        expect(wrapper.findComponent(AdminModal).exists()).toBe(false);
    });
    
    it("Отрисовка AdminBlock. Показ/Сокрытие AdminModal (is_admin: true)", async () => {
        const app = useAppStore();
        const filmsAccount = useFilmsAccountStore();
        
        const wrapper = mount(AdminBlock, {
            props: {
                user: {
                    id: 77,
                    is_admin: true,
                    login: 'TestLogin'
                }
            },
            global: {
                provide: { app, filmsAccount }
            }
        });
        
        const primaryButton = wrapper.getComponent(PrimaryButton);
        expect(primaryButton.props('buttonText')).toBe('Отказаться от администрирования');
        expect(primaryButton.props('handler')).toBe(wrapper.vm.showAdminModal);
        // В начальный момент AdminBlock скрыт
        expect(wrapper.findComponent(AdminModal).exists()).toBe(false);
        // Клик по кнопке открывает модальное окно
        await primaryButton.trigger('click');
        const adminModal = wrapper.findComponent(AdminModal);
        expect(adminModal.props('hideAdminModal')).toBe(wrapper.vm.hideAdminModal);
        expect(adminModal.props('admin')).toBe(true);
        // Клик по кнопке 'Нет' скрывает модальное окно
        const modalNo = wrapper.get('#modal-no');
        await modalNo.trigger('click');
        expect(wrapper.findComponent(AdminModal).exists()).toBe(false);
    });
});
