import { mount } from "@vue/test-utils";

import { setActivePinia, createPinia } from 'pinia';
import PersonalData from '@/Components/Pages/Auth/Account/PersonalDataBlock/PersonalData.vue';
import AccountRemoveBlock from '@/Components/Pages/Auth/Account/PersonalDataBlock/PersonalData/AccountRemoveBlock.vue';
import AdminBlock from '@/Components/Pages/Auth/Account/PersonalDataBlock/PersonalData/AdminBlock.vue';
import EmailBlock from '@/Components/Pages/Auth/Account/PersonalDataBlock/PersonalData/EmailBlock.vue';
import TokenBlock from '@/Components/Pages/Auth/Account/PersonalDataBlock/PersonalData/TokenBlock.vue';
import { useAppStore } from '@/Stores/app';

import { userAuth } from '@/__tests__/data/users';

const getWrapper = function(app) {
    return mount(PersonalData, {
            props: {
                user: userAuth
            },
            global: {
                provide: { app }
            }
        });
};

describe("@/Pages/Auth/Account/PersonalDataBlock/PersonalData.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    
    it("Отрисовка личных данных в ЛК", async () => {
        const app = useAppStore();
        
        const wrapper = getWrapper(app);
        
        // Проверяем наличие компонент
        expect(wrapper.findComponent(EmailBlock).props('user')).toStrictEqual(userAuth);
        expect(wrapper.findComponent(TokenBlock).exists()).toBe(true);
        expect(wrapper.findComponent(AdminBlock).props('user')).toStrictEqual(userAuth);
        expect(wrapper.findComponent(AccountRemoveBlock).exists()).toBe(true);
    });
});
