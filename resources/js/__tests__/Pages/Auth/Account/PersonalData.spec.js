import { mount } from "@vue/test-utils";
import { router } from '@inertiajs/vue3';

import '@/bootstrap';

import PersonalData from '@/Pages/Auth/Account/PersonalData.vue';
import CheckSvg from '@/Components/Svg/CheckSvg.vue';
import FormButton from '@/Components/Elements/FormButton.vue';

describe("@/Pages/Auth/Account/PersonalData.vue", () => {
    it("Отрисовка личных данных в ЛК (почта не подтверждена)", () => {
        const wrapper = mount(PersonalData, {
            props: {
                user: {
                    id: 77,
                    is_admin: false,
                    login: 'TestLogin',
                    email: 'test@example.com',
                    email_verified_at: null
                },
                token: null
            },
            global: {
                stubs: { FormButton: true }
            }
        });
        
        expect(wrapper.find('#personal-data').exists()).toBe(true);
        
        // Проверяем блок эл. почты
        const pdEmail = wrapper.get('#pd-email');
        expect(pdEmail.text()).toContain('Эл. почта:');
        expect(pdEmail.text()).toContain('test@example.com');
        expect(pdEmail.text()).toContain('Ваша эл. почта не подтверждена.');
        expect(pdEmail.findComponent(CheckSvg).exists()).toBe(false);
        expect(pdEmail.findComponent(FormButton).exists()).toBe(true);
    });
    
    it("Отрисовка личных данных в ЛК (почта подтверждена)", () => {
        const wrapper = mount(PersonalData, {
            props: {
                user: {
                    id: 77,
                    is_admin: false,
                    login: 'TestLogin',
                    email: 'test@example.com',
                    email_verified_at: "2023-05-16T17:11:39.000000Z"
                },
                token: null
            },
            global: {
                stubs: { FormButton: true }
            }
        });
        
        expect(wrapper.find('#personal-data').exists()).toBe(true);
        
        // Проверяем блок эл. почты
        const pdEmail = wrapper.get('#pd-email');
        expect(pdEmail.text()).toContain('Эл. почта:');
        expect(pdEmail.text()).toContain('test@example.com');
        expect(pdEmail.text()).not.toContain('Ваша эл. почта не подтверждена.');
        expect(pdEmail.findComponent(CheckSvg).exists()).toBe(true);
        expect(pdEmail.findComponent(FormButton).exists()).toBe(false);
    });
});
