import { describe, it, expect, } from "vitest";
import { mount, flushPromises } from "@vue/test-utils";

import { setActivePinia, createPinia } from 'pinia';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ForbiddenModal from '@/components/Modal/ForbiddenModal.vue';
import HouseSvg from '@/Components/Svg/HouseSvg.vue';
import { filmsAccountStore } from '@/Stores/films';

describe("@/Layouts/AdminLayout.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    
    it("Монтирование шаблона AdminLayout", () => {
        const filmsAccount = filmsAccountStore();
     
        const wrapper = mount(AdminLayout, {
            props: {
                errors: null
            },
            global: {
                mocks: {
                    $page: {
                        component: 'Admin/Home'
                    }
                },
                provide: { filmsAccount }
            }
        });

        // Присутствует навигация
        const nav = wrapper.find('nav');
        expect(nav.exists()).toBe(true);
        
        // В навигации 3 ссылки
        const li = nav.findAll('li');
        expect(li.length).toBe(3);
        
        // Первая ссылка активна ($page.component === 'Admin/Home')
        expect(li[0].find('a[href="/admin"]').exists()).toBe(true);
        expect(li[0].find('.router-link-active').exists()).toBe(true);
        // Содержит иконку HouseSvg
        expect(li[0].find('a[href="/admin"]').findComponent(HouseSvg).exists()).toBe(true);

        // Вторая ссылка 'города' не активна
        expect(li[1].find('a[href="/cities"]').exists()).toBe(true);
        expect(li[1].find('.router-link-active').exists()).toBe(false);
        expect(li[1].find('a[href="/cities"]').text()).toBe('города');

        // Третья ссылка 'лк' не активна с дефолтным url
        expect(li[2].find('a[href="/account?page=1&number=20&title=&description="]').exists()).toBe(true);
        expect(li[2].find('.router-link-active').exists()).toBe(false);
        expect(li[2].find('a[href="/account?page=1&number=20&title=&description="]').text()).toBe('лк');
        
        // Присутствует пустая компонента ForbiddenModal
        const forbiddenModal = wrapper.findComponent(ForbiddenModal);
        expect(forbiddenModal.exists()).toBe(true);
        expect(forbiddenModal.html()).toBe('<!--v-if-->');
    });
});
