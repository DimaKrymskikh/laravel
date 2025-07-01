import { mount } from "@vue/test-utils";

import { setActivePinia, createPinia } from 'pinia';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ForbiddenModal from '@/components/Modal/ForbiddenModal.vue';
import HouseSvg from '@/Components/Svg/HouseSvg.vue';
import GlobalModal from '@/components/Modal/GlobalModal.vue';
import AdminContentTabs from '@/components/Tabs/AdminContentTabs.vue';
import * as mod from '@/Services/inertia';
import { useFilmsAccountStore } from '@/Stores/films';
import { app } from '@/Services/app';

vi.spyOn(mod, 'useGlobalRequest');

const getWrapper = function() {
    return mount(AdminLayout, {
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
                provide: {
                    filmsAccount: useFilmsAccountStore()
                }
            }
        });
};

describe("@/Layouts/AdminLayout.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
        app.isRequest = false;
    });
    
    it("Монтирование шаблона AdminLayout", () => {
        const wrapper = getWrapper();

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
        expect(a1.attributes('href')).toBe(wrapper.vm.filmsAccount.getUrl('/userfilms'));
        expect(a1.classes('router-link-active')).toBe(false);
        expect(a1.text()).toBe('лк');
        
        // Присутствует пустая компонента ForbiddenModal
        const forbiddenModal = wrapper.findComponent(ForbiddenModal);
        expect(forbiddenModal.exists()).toBe(true);
        expect(forbiddenModal.html()).toBe('<!--v-if-->');
    });
    
    it("Видна компонента GlobalModal", async () => {
        mod.useGlobalRequest.mockReturnValue(true);
        const wrapper = getWrapper();

        expect(wrapper.findComponent(GlobalModal).exists()).toBe(true);
    });
    
    it("Компонента GlobalModal не видна, т.к. app.isRequest = true", async () => {
        mod.useGlobalRequest.mockReturnValue(true);
        app.isRequest = true;
        const wrapper = getWrapper(true);

        expect(wrapper.findComponent(GlobalModal).exists()).toBe(false);
    });
});
