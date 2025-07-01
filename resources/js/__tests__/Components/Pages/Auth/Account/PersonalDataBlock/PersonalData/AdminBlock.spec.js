import { mount } from "@vue/test-utils";

import { setActivePinia, createPinia } from 'pinia';
import AdminBlock from '@/Components/Pages/Auth/Account/PersonalDataBlock/PersonalData/AdminBlock.vue';
import AdminModal from '@/Components/Modal/Request/AdminModal.vue';
import PrimaryButton from '@/Components/Buttons/Variants/PrimaryButton.vue';
import { useFilmsAccountStore } from '@/Stores/films';

import { userAuth, userAdmin } from '@/__tests__/data/users';
import { AuthAccountLayoutStub } from '@/__tests__/stubs/layout';

const getWrapper = function(user, filmsAccount) {
        return mount(AdminBlock, {
            props: { user },
            global: {
                stubs: {
                    AccountLayout: AuthAccountLayoutStub
                },
                provide: { filmsAccount }
            }
        });
    };
    
const textForAuth = 'Нажмите кнопку "Сделать себя админом", чтобы получить права админа.';
const textForAdmin = 'Нажмите кнопку "Отказаться от администрирования", чтобы не быть админом.';

describe("@/Pages/Auth/Account/PersonalDataBlock/PersonalData/AdminBlock.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    
    it("Отрисовка AdminBlock. Показ/Сокрытие AdminModal (is_admin: false)", async () => {
        const filmsAccount = useFilmsAccountStore();
        
        const wrapper = getWrapper(userAuth, filmsAccount);
        
        expect(wrapper.text()).toContain(textForAuth);
        expect(wrapper.text()).not.toContain(textForAdmin);
        
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
        const filmsAccount = useFilmsAccountStore();
        
        const wrapper = getWrapper(userAdmin, filmsAccount);
        
        expect(wrapper.text()).not.toContain(textForAuth);
        expect(wrapper.text()).toContain(textForAdmin);
        
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
