import { mount } from "@vue/test-utils";
import { router } from '@inertiajs/vue3';

import '@/bootstrap';

import { setActivePinia, createPinia } from 'pinia';
import Token from '@/Pages/Auth/Token.vue';
import { useAppStore } from '@/Stores/app';
import { useFilmsListStore, useFilmsAccountStore } from '@/Stores/films';

// Делаем заглушку для Head
vi.mock('@inertiajs/vue3', async () => {
    const actual = await vi.importActual("@inertiajs/vue3");
    return {
        ...actual,
        Head: vi.fn()
    };
});

describe("@/Pages/Auth/Token.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    
    it("Отрисовка страницы с токеном", () => {
        const app = useAppStore();
        const filmsList = useFilmsListStore();
        const filmsAccount = useFilmsAccountStore();
        
        const wrapper = mount(Token, {
            props: {
                user: {
                    id: 77,
                    is_admin: false
                },
                token: 'TestToken'
            },
            global: {
                mocks: {
                    $page: {
                        component: 'Auth/Token'
                    }
                },
                provide: { app, filmsList, filmsAccount }
            }
        });
        
        expect(wrapper.get('h1').text()).toBe('Токен');
        expect(wrapper.text()).toContain('Сохраните полученный токен:');
        expect(wrapper.text()).toContain('TestToken');
        const accountLink = wrapper.get('#account-link');
        expect(accountLink.attributes('href')).toBe(filmsAccount.getUrl('/account'));
        expect(accountLink.text()).toBe('Перейти в личный кабинет');
    });
});
