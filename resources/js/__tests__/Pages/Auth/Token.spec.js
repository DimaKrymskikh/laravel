import { mount } from "@vue/test-utils";
import { router } from '@inertiajs/vue3';

import '@/bootstrap';

import { setActivePinia, createPinia } from 'pinia';
import Token from '@/Pages/Auth/Token.vue';
import { filmsCatalogStore, filmsAccountStore } from '@/Stores/films';

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
        const filmsCatalog = filmsCatalogStore();
        const filmsAccount = filmsAccountStore();
        
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
                provide: { filmsCatalog, filmsAccount }
            }
        });
        
        expect(wrapper.get('h1').text()).toBe('Токен');
        expect(wrapper.text()).toContain('Сохраните полученный токен:');
        expect(wrapper.text()).toContain('TestToken');
        const accountLink = wrapper.get('#account-link');
        expect(accountLink.attributes('href')).toBe(filmsAccount.getUrl());
        expect(accountLink.text()).toBe('Перейти в личный кабинет');
    });
});
