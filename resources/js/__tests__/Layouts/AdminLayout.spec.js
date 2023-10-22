import { mount, flushPromises } from "@vue/test-utils";

import { setActivePinia, createPinia } from 'pinia';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ForbiddenModal from '@/components/Modal/ForbiddenModal.vue';
import HouseSvg from '@/Components/Svg/HouseSvg.vue';
import AdminContentTabs from '@/components/Tabs/AdminContentTabs.vue';
import { useAppStore } from '@/Stores/app';
import { useFilmsAccountStore } from '@/Stores/films';

describe("@/Layouts/AdminLayout.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    
    it("Монтирование шаблона AdminLayout", () => {
        const app = useAppStore();
        const filmsAccount = useFilmsAccountStore();
     
        const wrapper = mount(AdminLayout, {
            props: {
                errors: null
            },
            global: {
                stubs: {
                    AdminContentTabs: true
                },
                mocks: {
                    $page: {
                        component: 'Admin/Home'
                    }
                },
                provide: { app, filmsAccount }
            }
        });

        // Присутствует навигация
        const nav = wrapper.find('nav');
        expect(nav.exists()).toBe(true);
        
        // В навигации 2 ссылки
        const li = nav.findAll('li');
        expect(li.length).toBe(2);
        
        // Первая ссылка активна ($page.component === 'Admin/Home')
        const a0 = li[0].get('a');
        expect(a0.attributes('href')).toBe('/admin');
        expect(a0.classes('router-link-active')).toBe(true);
        // Содержит иконку HouseSvg
        expect(a0.findComponent(HouseSvg).exists()).toBe(true);

        // Вторая ссылка 'лк' не активна с дефолтным url
        const a1 = li[1].get('a');
        expect(a1.attributes('href')).toBe('/userfilms?page=1&number=20&title=&description=');
        expect(a1.classes('router-link-active')).toBe(false);
        expect(a1.text()).toBe('лк');
        
        // Присутствует пустая компонента ForbiddenModal
        const forbiddenModal = wrapper.findComponent(ForbiddenModal);
        expect(forbiddenModal.exists()).toBe(true);
        expect(forbiddenModal.html()).toBe('<!--v-if-->');
    });
});
