import { mount } from "@vue/test-utils";

import Fallback from "@/Pages/Auth/Fallback.vue";
import AuthLayout from '@/Layouts/AuthLayout.vue';
import { AuthLayoutStub } from '@/__tests__/stubs/layout';

// Делаем заглушку для Head
vi.mock('@inertiajs/vue3', async () => {
    const actual = await vi.importActual("@inertiajs/vue3");
    return {
        ...actual,
        Head: vi.fn()
    };
});

describe("@/Pages/Auth/Fallback.vue", () => {
    it("Отрисовка страницы при 404 (залогиненный пользователь)", () => {
        const wrapper = mount(Fallback, {
            props: {
                errors: {}
            },
            global: {
                stubs: {
                    AuthLayout: AuthLayoutStub
                }
            }
        });
        
        expect(wrapper.findComponent(AuthLayout).exists()).toBe(true);
        
        const h1 = wrapper.get('h1');
        expect(h1.text()).toBe('Страница не найдена');
    });
});
