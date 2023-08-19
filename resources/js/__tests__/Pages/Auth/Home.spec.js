import { mount } from "@vue/test-utils";

import '@/bootstrap';

import { setActivePinia, createPinia } from 'pinia';
import Home from "@/Pages/Auth/Home.vue";
import BreadCrumb from '@/Components/Elements/BreadCrumb.vue';
import { filmsCatalogStore, filmsAccountStore } from '@/Stores/films';

// Делаем заглушку для Head
vi.mock('@inertiajs/vue3', async () => {
    const actual = await vi.importActual("@inertiajs/vue3");
    return {
        ...actual,
        Head: vi.fn()
    };
});

describe("@/Pages/Auth/Home.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    
    it("Отрисовка домашней страницы (залогиненный пользователь)", () => {
        const filmsCatalog = filmsCatalogStore();
        const filmsAccount = filmsAccountStore();
        
        const wrapper = mount(Home, {
            props: {
                user: {
                    id: 77,
                    is_admin: false
                },
                errors: null
            },
            global: {
                mocks: {
                    $page: {
                        component: 'Auth/Home'
                    }
                },
                provide: { filmsCatalog, filmsAccount }
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
