import { mount } from "@vue/test-utils";

import Home from "@/Pages/Guest/Home.vue";
import GuestLayout from '@/Layouts/GuestLayout.vue';
import BreadCrumb from '@/Components/Elements/BreadCrumb.vue';
import { GuestLayoutStub } from '@/__tests__/stubs/layout';

// Делаем заглушку для Head
vi.mock('@inertiajs/vue3', async () => {
    const actual = await vi.importActual("@inertiajs/vue3");
    return {
        ...actual,
        Head: vi.fn()
    };
});

describe("@/Pages/Guest/Home.vue", () => {
    it("Отрисовка домашней страницы (гостевой режим)", () => {
        const wrapper = mount(Home, {
            props: {
                errors: {}
            },
            global: {
                stubs: {
                    GuestLayout: GuestLayoutStub
                }
            }
        });
        
        expect(wrapper.findComponent(GuestLayout).exists()).toBe(true);
        
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
