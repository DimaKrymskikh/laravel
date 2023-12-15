import { mount } from "@vue/test-utils";
import { setActivePinia, createPinia } from 'pinia';

import AdminContentTabs from '@/components/Tabs/AdminContentTabs.vue';
import { useActorsListStore } from '@/Stores/actors';

describe("@/components/Tabs/AdminContentTabs.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    
    it("Монтирование AdminContentTabs", async () => {
        const actorsList = useActorsListStore();
        
        const wrapper = mount(AdminContentTabs, {
            global: {
                mocks: {
                    $page: {
                        component: 'Admin/Cities'
                    }
                },
                provide: { actorsList }
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
        expect(lis.length).toBe(3);
        
        const a0 = lis[0].get('a');
        expect(a0.attributes('href')).toBe('/admin/cities');
        expect(a0.classes('tabs-link-active')).toBe(true);
        expect(a0.text()).toBe('города');
        
        const a1 = lis[1].get('a');
        expect(a1.attributes('href')).toBe('/admin/languages');
        expect(a1.classes('tabs-link-active')).toBe(false);
        expect(a1.text()).toBe('языки');
        
        const a2 = lis[2].get('a');
        expect(a2.attributes('href')).toBe(actorsList.getUrl());
        expect(a2.classes('tabs-link-active')).toBe(false);
        expect(a2.text()).toBe('актёры');
        
        // Повторный клик по кнопке 'контент' скрывает список
        await span.trigger('click');
        expect(wrapper.find('ul').exists()).toBe(false);
    });
});
