import { mount } from "@vue/test-utils";

import { setActivePinia, createPinia } from 'pinia';
import Home from "@/Pages/Guest/Home.vue";
import BreadCrumb from '@/Components/Elements/BreadCrumb.vue';
import { filmsCatalogStore } from '@/Stores/films';

// Делаем заглушку для Head
vi.mock('@inertiajs/vue3', async () => {
    const actual = await vi.importActual("@inertiajs/vue3");
    return {
        ...actual,
        Head: vi.fn()
    };
});

describe("@/Pages/Guest/Home.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    
    it("Отрисовка домашней страницы (гостевой режим)", () => {
        const filmsCatalog = filmsCatalogStore();
        
        const wrapper = mount(Home, {
            props: {
                errors: null
            },
            global: {
                mocks: {
                    $page: {
                        component: 'Guest/Catalog'
                    }
                },
                provide: { filmsCatalog }
            }
        });
        
        const h1 = wrapper.get('h1');
        expect(h1.text()).toBe('Главная страница');
        
        const breadCrumb = wrapper.findComponent(BreadCrumb);
        expect(breadCrumb.exists()).toBe(true);
        
        // Хлебные крошки состоят из одного элемента без ссылки
        const li = breadCrumb.findAll('li');
        expect(li.length).toBe(1);
        expect(li[0].text()).toBe('Главная страница');
        expect(li[0].find('a').exists()).toBe(false);
    });
});
