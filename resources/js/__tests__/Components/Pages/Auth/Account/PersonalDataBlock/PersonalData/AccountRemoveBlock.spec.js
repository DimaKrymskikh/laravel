import { mount } from "@vue/test-utils";

import { setActivePinia, createPinia } from 'pinia';
import AccountRemoveBlock from '@/Components/Pages/Auth/Account/PersonalDataBlock/PersonalData/AccountRemoveBlock.vue';
import DangerButton from '@/Components/Buttons/Variants/DangerButton.vue';
import AccountRemoveModal from '@/Components/Modal/Request/AccountRemoveModal.vue';

const getWrapper = function(app) {
    return mount(AccountRemoveBlock);
};

describe("@/Pages/Auth/Account/PersonalDataBlock/PersonalData/AccountRemoveBlock.vue", () => {
    it("Отрисовка AccountRemoveBlock. Показ/Сокрытие AccountRemoveModal", async () => {
        const wrapper = getWrapper();
        
        const dangerButton = wrapper.getComponent(DangerButton);
        expect(dangerButton.props('buttonText')).toBe('Удалить аккаунт');
        expect(dangerButton.props('handler')).toBe(wrapper.vm.showAccountRemoveModal);
        // В начальный момент AccountRemoveModal скрыт
        expect(wrapper.findComponent(AccountRemoveModal).exists()).toBe(false);
        // Клик по кнопке открывает модальное окно
        await dangerButton.trigger('click');
        const accountRemoveModal = wrapper.findComponent(AccountRemoveModal);
        expect(accountRemoveModal.props('hideAccountRemoveModal')).toBe(wrapper.vm.hideAccountRemoveModal);
        // Клик по кнопке 'Нет' скрывает модальное окно
        const modalNo = wrapper.get('#modal-no');
        await modalNo.trigger('click');
        expect(wrapper.findComponent(AccountRemoveModal).exists()).toBe(false);
    });
});
