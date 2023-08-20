import { mount } from "@vue/test-utils";

import '@/bootstrap';

import { setActivePinia, createPinia } from 'pinia';
import FilmCard from "@/Pages/Auth/FilmCard.vue";
import BreadCrumb from '@/Components/Elements/BreadCrumb.vue';
import { filmsCatalogStore, filmsAccountStore } from '@/Stores/films';

import { filmCard } from '@/__tests__/data/films';

// Делаем заглушку для Head
vi.mock('@inertiajs/vue3', async () => {
    const actual = await vi.importActual("@inertiajs/vue3");
    return {
        ...actual,
        Head: vi.fn()
    };
});

describe("@/Pages/Auth/FilmCard.vue", () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });
    
    it("Отрисовка карточки фильма", () => {
        const filmsCatalog = filmsCatalogStore();
        const filmsAccount = filmsAccountStore();
        
        const wrapper = mount(FilmCard, {
            props: {
                errors: null,
                film: filmCard,
                user: {
                    id: 77,
                    is_admin: false
                }
            },
            global: {
                mocks: {
                    $page: {
                        component: 'Auth/FilmCard'
                    }
                },
                provide: { filmsCatalog, filmsAccount }
            }
        });
        
        // Отрисовывается заголовок страницы
        const h1 = wrapper.get('h1');
        expect(h1.text()).toBe(wrapper.vm.titlePage);
        
        // Отрисовываются хлебные крошки
        const breadCrumb = wrapper.findComponent(BreadCrumb);
        expect(breadCrumb.exists()).toBe(true);
        
        // Проверяем хлебные крошки
        const li = breadCrumb.findAll('li');
        expect(li.length).toBe(3);
        expect(li[0].find('a[href="/"]').exists()).toBe(true);
        expect(li[0].text()).toBe('Главная страница');
        expect(li[1].find('a').attributes('href')).toBe(filmsAccount.getUrl());
        expect(li[1].text()).toBe('ЛК');
        expect(li[2].find('a').exists()).toBe(false);
        expect(li[2].text()).toBe(wrapper.vm.titlePage);
        
        const h3 = wrapper.findAll('h3');
        expect(h3.length).toBe(3);
        expect(h3[0].text()).toBe('Основная информация');
        expect(h3[1].text()).toBe('Описание');
        expect(h3[2].text()).toBe('Актёры');
        
        expect(wrapper.text()).toContain(`Фильм вышел в ${filmCard.release_year} году`);
        expect(wrapper.text()).toContain(`Язык фильма: ${filmCard.language.name}`);
        expect(wrapper.text()).toContain(`${filmCard.description}`);
        expect(wrapper.text()).toContain(`${filmCard.actors[0].first_name} ${filmCard.actors[0].last_name}`);
        expect(wrapper.text()).toContain(`${filmCard.actors[1].first_name} ${filmCard.actors[1].last_name}`);
    });
});
