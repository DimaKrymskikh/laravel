import { mount } from "@vue/test-utils";

import { setActivePinia, createPinia } from 'pinia';
import AuthAccountTabs from '@/components/Tabs/AuthAccountTabs.vue';
import { useFilmsAccountStore } from '@/Stores/films';

describe("@/components/Tabs/AuthAccountTabs.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    
    it("Монтирование AuthAccountTabs", () => {
        const filmsAccount = useFilmsAccountStore();
        
        const wrapper = mount(AuthAccountTabs, {
            global: {
                mocks: {
                    $page: {
                        component: 'Auth/Account/UserFilms'
                    }
                },
                provide: { filmsAccount }
            }
        });
        
        const lis = wrapper.get('ul').findAll('li');
        expect(lis.length).toBe(2);
        
        const a0 = lis[0].get('a');
        expect(a0.attributes('href')).toBe(wrapper.vm.filmsAccount.getUrl('/userfilms'));
        expect(a0.text()).toBe('фильмы');
        expect(a0.classes('tabs-link-active')).toBe(true);
        
        const a1 = lis[1].get('a');
        expect(a1.attributes('href')).toBe('/userweather');
        expect(a1.text()).toBe('погода');
        expect(a1.classes('tabs-link-active')).toBe(false);
    });
});
