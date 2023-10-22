import { mount } from "@vue/test-utils";

import { setActivePinia, createPinia } from 'pinia';
import GuestContentTabs from '@/components/Tabs/GuestContentTabs.vue';
import { useFilmsListStore } from '@/Stores/films';

describe("@/components/Tabs/GuestContentTabs.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    
    it("Монтирование GuestContentTabs", async () => {
        const filmsList = useFilmsListStore();
        
        const wrapper = mount(GuestContentTabs, {
            global: {
                mocks: {
                    $page: {
                        component: 'Guest/Films'
                    }
                },
                provide: { filmsList }
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
        expect(a0.attributes('href')).toBe(filmsList.getUrl('/guest/films'));
        expect(a0.classes('tabs-link-active')).toBe(true);
        expect(a0.text()).toBe('фильмы');
        
        const a1 = lis[1].get('a');
        expect(a1.attributes('href')).toBe('/guest/cities');
        expect(a1.classes('tabs-link-active')).toBe(false);
        expect(a1.text()).toBe('города');
        
        // Повторный клик по кнопке 'контент' скрывает список
        await span.trigger('click');
        expect(wrapper.find('ul').exists()).toBe(false);
    });
});
