import { mount } from "@vue/test-utils";

import { setActivePinia, createPinia } from 'pinia';
import Home from "@/Pages/Admin/Home.vue";
import BreadCrumb from '@/Components/Elements/BreadCrumb.vue';
import { filmsAccountStore } from '@/Stores/films';

describe("@/Pages/Admin/Home.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    
    it("Отрисовка страницы 'Страница админа'", () => {
        const filmsAccount = filmsAccountStore();
        
        const wrapper = mount(Home, {
            props: {
                errors: null
            },
            global: {
                stubs: {
                    AdminLayout: false,
                    BreadCrumb: false
                },
                mocks: {
                    $page: {
                        component: 'Admin/Home'
                    }
                },
                provide: { filmsAccount }
            },
            shallow: true
        });
        
        const h1 = wrapper.get('h1');
        expect(h1.text()).toBe('Страница админа');
        
        const breadCrumb = wrapper.findComponent(BreadCrumb);
        expect(breadCrumb.exists()).toBe(true);
        
        // Хлебные крошки состоят из одного элемента без ссылки
        const li = breadCrumb.findAll('li');
        expect(li.length).toBe(1);
        expect(li[0].text()).toBe('Страница админа');
        expect(li[0].find('a').exists()).toBe(false);
    });
});
