import { mount } from "@vue/test-utils";

import Fallback from "@/Pages/Guest/Fallback.vue";
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { GuestLayoutStub } from '@/__tests__/stubs/layout';

// Делаем заглушку для Head
vi.mock('@inertiajs/vue3', async () => {
    const actual = await vi.importActual("@inertiajs/vue3");
    return {
        ...actual,
        Head: vi.fn()
    };
});

describe("@/Pages/Guest/Fallback.vue", () => {
    it("Отрисовка страницы при 404 (гостевой режим)", () => {
        const wrapper = mount(Fallback, {
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
        expect(h1.text()).toBe('Страница не найдена');
    });
});
