import { mount } from "@vue/test-utils";
import { Link } from '@inertiajs/vue3';

import { setActivePinia, createPinia } from 'pinia';
import GuestContentTabs from '@/components/Tabs/GuestContentTabs.vue';
import { useFilmsListStore } from '@/Stores/films';

const getWrapper = function(filmsList, component) {
    return mount(GuestContentTabs, {
            global: {
                mocks: {
                    $page: {
                        component
                    }
                },
                provide: { filmsList }
            }
        });
};

describe("@/components/Tabs/GuestContentTabs.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    
    it("Монтирование GuestContentTabs ('router-link-active': true)", async () => {
        const filmsList = useFilmsListStore();
        
        const wrapper = getWrapper(filmsList, 'Guest/Films');
        
        // В начальный момент список отсутствует
        expect(wrapper.find('ul').exists()).toBe(false);
        
        const span = wrapper.get('span');
        expect(span.text()).toBe('контент');
        expect(span.classes('router-link-active')).toBe(true);
        
        // Клик по кнопке 'контент' открывает список
        await span.trigger('click');
        const ul = wrapper.get('ul');
        const links = ul.findAllComponents(Link);
        expect(links.length).toBe(2);
        // Первая кнопка активная (component: 'Guest/Films')
        expect(links[0].attributes('href')).toBe(filmsList.getUrl('/guest/films'));
        expect(links[0].classes('tabs-link-active')).toBe(true);
        expect(links[0].text()).toBe('фильмы');
        // Вторая кнопка неактивная
        expect(links[1].attributes('href')).toBe('/guest/cities');
        expect(links[1].classes('tabs-link-active')).toBe(false);
        expect(links[1].text()).toBe('города');
        
        // Повторный клик по кнопке 'контент' скрывает список
        await span.trigger('click');
        expect(wrapper.find('ul').exists()).toBe(false);
    });
    
    it("Монтирование GuestContentTabs ('router-link-active': false)", async () => {
        const filmsList = useFilmsListStore();
        
        const wrapper = getWrapper(filmsList, 'Guest/Login');
        
        // В начальный момент список отсутствует
        expect(wrapper.find('ul').exists()).toBe(false);
        
        const span = wrapper.get('span');
        expect(span.text()).toBe('контент');
        expect(span.classes('router-link-active')).toBe(false);
        
        // Клик по кнопке 'контент' открывает список
        await span.trigger('click');
        const ul = wrapper.get('ul');
        const links = ul.findAllComponents(Link);
        expect(links.length).toBe(2);
        // Обе кнопки неактивные
        expect(links[0].attributes('href')).toBe(filmsList.getUrl('/guest/films'));
        expect(links[0].classes('tabs-link-active')).toBe(false);
        expect(links[0].text()).toBe('фильмы');
        expect(links[1].attributes('href')).toBe('/guest/cities');
        expect(links[1].classes('tabs-link-active')).toBe(false);
        expect(links[1].text()).toBe('города');
        
        // Повторный клик по кнопке 'контент' скрывает список
        await span.trigger('click');
        expect(wrapper.find('ul').exists()).toBe(false);
    });
});
