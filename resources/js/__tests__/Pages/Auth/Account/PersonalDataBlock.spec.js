import { mount } from "@vue/test-utils";

import { setActivePinia, createPinia } from 'pinia';
import Bars3 from '@/Components/Svg/Bars3.vue';
import PersonalDataBlock from '@/Pages/Auth/Account/PersonalDataBlock.vue';
import PersonalData from '@/Pages/Auth/Account/PersonalDataBlock/PersonalData.vue';
import { useAppStore } from '@/Stores/app';

describe("@/Pages/Auth/Account/PersonalDataBlock.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    
    it("Отрисовка PersonalDataBlock. Показ/Скрытие PersonalData", async () => {
        const app = useAppStore();
        
        const wrapper = mount(PersonalDataBlock, {
            props: {
                user: {
                    id: 77,
                    is_admin: false,
                    login: 'TestLogin',
                    email: 'test@example.com',
                    email_verified_at: null
                }
            },
            global: {
                provide: { app }
            }
        });
        
        const bars3 = wrapper.getComponent(Bars3);
        // В начальный момент PersonalData отсутствует
        expect(wrapper.findComponent(PersonalData).exists()).toBe(false);
        // Клик по кнопке открывает PersonalData
        await bars3.trigger('click');
        const personalData = wrapper.findComponent(PersonalData);
        expect(personalData.exists()).toBe(true);
        expect(personalData.props('user')).toBe(wrapper.props('user'));
        // Повторный клик скрывает PersonalData
        await bars3.trigger('click');
        expect(wrapper.findComponent(PersonalData).exists()).toBe(false);
    });
});
