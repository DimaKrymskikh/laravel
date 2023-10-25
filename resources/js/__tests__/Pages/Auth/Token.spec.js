import { mount } from "@vue/test-utils";
import { router } from '@inertiajs/vue3';

import '@/bootstrap';

import { setActivePinia, createPinia } from 'pinia';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import Token from '@/Pages/Auth/Token.vue';
import { useFilmsAccountStore } from '@/Stores/films';

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

describe("@/Pages/Auth/Token.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    
    it("Отрисовка страницы с токеном", () => {
        const filmsAccount = useFilmsAccountStore();
        
        const wrapper = mount(Token, {
            props: {
                user,
                errors: {},
                token: 'TestToken'
            },
            global: {
                stubs: {
                    AuthLayout: AuthLayoutStub
                },
                provide: { filmsAccount }
            }
        });
        
        const authLayout = wrapper.getComponent(AuthLayout);
        expect(authLayout.props('user')).toStrictEqual(user);
        expect(authLayout.props('errors')).toStrictEqual({});
        
        expect(wrapper.get('h1').text()).toBe('Токен');
        expect(wrapper.text()).toContain('Сохраните полученный токен:');
        expect(wrapper.text()).toContain('TestToken');
        const accountLink = wrapper.get('#account-link');
        expect(accountLink.attributes('href')).toBe(filmsAccount.getUrl('/userfilms'));
        expect(accountLink.text()).toBe('Перейти в личный кабинет');
    });
});
