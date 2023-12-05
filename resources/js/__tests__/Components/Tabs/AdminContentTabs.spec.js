import { mount } from "@vue/test-utils";

import AdminContentTabs from '@/components/Tabs/AdminContentTabs.vue';

describe("@/components/Tabs/AdminContentTabs.vue", () => {
    it("Монтирование AdminContentTabs", async () => {
        const wrapper = mount(AdminContentTabs, {
            global: {
                mocks: {
                    $page: {
                        component: 'Admin/Cities'
                    }
                }
            }
        });
        
        // В начальный момент список отсутствует
        expect(wrapper.find('ul').exists()).toBe(false);
        
        const span = wrapper.get('span');
        expect(span.text()).toBe('контент');
        expect(span.classes('router-link-active')).toBe(true);
        
        // Клик по кнопке 'контент' открывает список
        await span.trigger('click');
        const ul = wrapper.get('ul');
        const lis = ul.findAll('li');
        expect(lis.length).toBe(2);
        
        const a0 = lis[0].get('a');
        expect(a0.attributes('href')).toBe('/admin/cities');
        expect(a0.classes('tabs-link-active')).toBe(true);
        expect(a0.text()).toBe('города');
        
        const a1 = lis[1].get('a');
        expect(a1.attributes('href')).toBe('/admin/languages');
        expect(a1.classes('tabs-link-active')).toBe(false);
        expect(a1.text()).toBe('языки');
        
        // Повторный клик по кнопке 'контент' скрывает список
        await span.trigger('click');
        expect(wrapper.find('ul').exists()).toBe(false);
    });
});
