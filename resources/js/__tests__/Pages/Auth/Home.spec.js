import { mount } from "@vue/test-utils";

import Home from "@/Pages/Auth/Home.vue";
import AuthLayout from '@/Layouts/AuthLayout.vue';
import BreadCrumb from '@/Components/Elements/BreadCrumb.vue';

import { AuthLayoutStub } from '@/__tests__/stubs/layout';

// Делаем заглушку для Head
vi.mock('@inertiajs/vue3', async () => {
    const actual = await vi.importActual("@inertiajs/vue3");
    return {
        ...actual,
        Head: vi.fn()
    };
});

const user = {
            id: 77,
            is_admin: false
        };

describe("@/Pages/Auth/Home.vue", () => {
    it("Отрисовка домашней страницы (залогиненный пользователь)", () => {
        const wrapper = mount(Home, {
            props: {
                user,
                errors: {}
            },
            global: {
                stubs: {
                    AuthLayout: AuthLayoutStub
                }
            }
        });
        
        const authLayout = wrapper.getComponent(AuthLayout);
        expect(authLayout.props('user')).toStrictEqual(user);
        expect(authLayout.props('errors')).toStrictEqual({});
        
        const h1 = wrapper.get('h1');
        expect(h1.text()).toBe('Главная страница');
        
        const breadCrumb = wrapper.getComponent(BreadCrumb);
        expect(breadCrumb.props('linksList')).toBe(wrapper.vm.linksList);
    });
});
