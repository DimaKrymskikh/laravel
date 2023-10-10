import { mount } from "@vue/test-utils";

import { setActivePinia, createPinia } from 'pinia';
import PersonalData from '@/Pages/Auth/Account/PersonalDataBlock/PersonalData.vue';
import EmailBlock from '@/Pages/Auth/Account/PersonalDataBlock/PersonalData/EmailBlock.vue';
import TokenBlock from '@/Pages/Auth/Account/PersonalDataBlock/PersonalData/TokenBlock.vue';
import { useAppStore } from '@/Stores/app';

describe("@/Pages/Auth/Account/PersonalDataBlock/PersonalData.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    
    it("Отрисовка личных данных в ЛК", async () => {
        const app = useAppStore();
        
        const wrapper = mount(PersonalData, {
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
        
        // Компоненте EmailBlock передаётся props
        expect(wrapper.findComponent(EmailBlock).props('user')).toBe(wrapper.props('user'));
        
        expect(wrapper.findComponent(TokenBlock).exists()).toBe(true);
    });
});
