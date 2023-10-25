import { mount } from "@vue/test-utils";

import Home from "@/Pages/Admin/Home.vue";
import AdminLayout from '@/Layouts/AdminLayout.vue';
import BreadCrumb from '@/Components/Elements/BreadCrumb.vue';

import { AdminLayoutStub } from '@/__tests__/stubs/layout';

// Делаем заглушку для Head
vi.mock('@inertiajs/vue3', async () => {
    const actual = await vi.importActual("@inertiajs/vue3");
    return {
        ...actual,
        Head: vi.fn()
    };
});

describe("@/Pages/Admin/Home.vue", () => {
    it("Отрисовка страницы 'Страница админа'", () => {
        const wrapper = mount(Home, {
            props: {
                errors: {}
            },
            global: {
                stubs: {
                    AdminLayout: AdminLayoutStub
                }
            }
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
