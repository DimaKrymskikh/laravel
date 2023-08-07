import { mount } from "@vue/test-utils";

import BreadCrumb from '@/Components/Elements/BreadCrumb.vue';

describe("@/Components/Elements/BreadCrumb.vue", () => {
    it("Монтирование компоненты", () => {
        const wrapper = mount(BreadCrumb, {
            props: {
                linksList: [{
                    link: '/',
                    text: 'Главная страница'
                }, {
                    link: '/another',
                    text: 'Другая страница'
                }, {
                    text: 'Данная страница'
                }]
            }
        });
       
        const li = wrapper.findAll('li');
        // Присутствует ссылка на 'Главную страницу'
        expect(li[0].exists()).toBe(true);
        expect(li[0].find('a[href="/"]').exists()).toBe(true);
        expect(li[0].text()).toBe('Главная страница');
        // Присутствует ссылка на 'Другую страницу'
        expect(li[1].exists()).toBe(true);
        expect(li[1].find('a[href="/another"]').exists()).toBe(true);
        expect(li[1].text()).toBe('Другая страница');
        // 'Данная страница' - страница посещения
        expect(li[2].exists()).toBe(true);
        expect(li[2].find('a').exists()).toBe(false);
        expect(li[2].text()).toBe('Данная страница');
        // Других элементов в хлебных крошках нет
        expect(li.length).toBe(3);
    });
});
