import { mount } from "@vue/test-utils";

import Fallback from "@/Pages/Admin/Fallback.vue";
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { AdminLayoutStub } from '@/__tests__/stubs/layout';

// Делаем заглушку для Head
vi.mock('@inertiajs/vue3', async () => {
    const actual = await vi.importActual("@inertiajs/vue3");
    return {
        ...actual,
        Head: vi.fn()
    };
});

describe("@/Pages/Admin/Fallback.vue", () => {
    it("Отрисовка страницы при 404 (админ)", () => {
        const wrapper = mount(Fallback, {
            props: {
                errors: {}
            },
            global: {
                stubs: {
                    AdminLayout: AdminLayoutStub
                }
            }
        });
        
        expect(wrapper.findComponent(AdminLayout).exists()).toBe(true);
        
        const h1 = wrapper.get('h1');
        expect(h1.text()).toBe('Страница не найдена');
    });
});
