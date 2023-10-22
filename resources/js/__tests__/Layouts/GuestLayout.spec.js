import { mount } from "@vue/test-utils";

import { setActivePinia, createPinia } from 'pinia';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import ForbiddenModal from '@/components/Modal/ForbiddenModal.vue';
import HouseSvg from '@/Components/Svg/HouseSvg.vue';
import GuestContentTabs from '@/components/Tabs/GuestContentTabs.vue';
import { useAppStore } from '@/Stores/app';
import { useFilmsListStore } from '@/Stores/films';

describe("@/Layouts/GuestLayout.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    
    it("Монтирование шаблона GuestLayout", () => {
        const app = useAppStore();
        const filmsList = useFilmsListStore();
        
        const wrapper = mount(GuestLayout, {
            global: {
                stubs: {
                    GuestContentTabs: true
                },
                mocks: {
                    $page: {
                        component: 'Guest/Home'
                    }
                },
                provide: { app, filmsList }
            }
        });

        // Присутствует навигация
        const nav = wrapper.find('nav');
        expect(nav.exists()).toBe(true);
        
        // В навигации 2 ссылки
        const li = nav.findAll('li');
        expect(li.length).toBe(2);
        
        // Первая вкладка - активная ссылка ($page.component === 'Guest/Home')
        const a0 = li[0].find('a');
        expect(a0.attributes('href')).toBe('/guest');
        expect(a0.attributes('class')).toContain('router-link-active');
        // Содержит иконку HouseSvg
        expect(a0.findComponent(HouseSvg).exists()).toBe(true);
        
        // Вторая вкладка - неактивная ссылка 'вход'
        const a1 = li[1].find('a');
        expect(a1.attributes('href')).toBe('/login');
        expect(a1.attributes('class')).not.toContain('router-link-active');
        expect(a1.text()).toBe('вход');
        
        // Присутствует пустая компонента ForbiddenModal
        const forbiddenModal = wrapper.findComponent(ForbiddenModal);
        expect(forbiddenModal.exists()).toBe(true);
        expect(forbiddenModal.html()).toBe('<!--v-if-->');
    });
});
