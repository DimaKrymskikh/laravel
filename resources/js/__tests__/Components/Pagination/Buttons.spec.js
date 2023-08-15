import { mount } from "@vue/test-utils";

import Buttons from '@/Components/Pagination/Buttons.vue';

import { links_1, links_5, links_last } from '@/__tests__/data/links';

describe("@/Components/Pagination/Buttons.vue", () => {
    it("Монтирование компоненты. Активна первая страница", () => {
        const wrapper = mount(Buttons, {
            props: {
                links: links_1
            }
        });
        
        const nav = wrapper.get('nav');
        expect(nav.isVisible()).toBe(true);
        
        const span = nav.findAll('span');
        expect(span.length).toBe(3);
        expect(span[0].text()).toBe('');
        expect(span[1].text()).toBe('1');
        expect(span[2].text()).toBe('...');
        
        const a = nav.findAll('a');
        expect(a.length).toBe(12);
        expect(a[0].text()).toBe('2');
        expect(a[1].text()).toBe('3');
        expect(a[2].text()).toBe('4');
        expect(a[3].text()).toBe('5');
        expect(a[4].text()).toBe('6');
        expect(a[5].text()).toBe('7');
        expect(a[6].text()).toBe('8');
        expect(a[7].text()).toBe('9');
        expect(a[8].text()).toBe('10');
        expect(a[9].text()).toBe('49');
        expect(a[10].text()).toBe('50');
        expect(a[11].text()).toBe('');
    });
    
    it("Монтирование компоненты. Активна 5-тая страница", () => {
        const wrapper = mount(Buttons, {
            props: {
                links: links_5
            }
        });
        
        const nav = wrapper.get('nav');
        expect(nav.isVisible()).toBe(true);
        
        const span = nav.findAll('span');
        expect(span.length).toBe(2);
        expect(span[0].text()).toBe('5');
        expect(span[1].text()).toBe('...');
        
        const a = nav.findAll('a');
        expect(a.length).toBe(13);
        expect(a[0].text()).toBe('');
        expect(a[1].text()).toBe('1');
        expect(a[2].text()).toBe('2');
        expect(a[3].text()).toBe('3');
        expect(a[4].text()).toBe('4');
        expect(a[5].text()).toBe('6');
        expect(a[6].text()).toBe('7');
        expect(a[7].text()).toBe('8');
        expect(a[8].text()).toBe('9');
        expect(a[9].text()).toBe('10');
        expect(a[10].text()).toBe('99');
        expect(a[11].text()).toBe('100');
        expect(a[12].text()).toBe('');
    });
    
    it("Монтирование компоненты. Активна последняя страница", () => {
        const wrapper = mount(Buttons, {
            props: {
                links: links_last
            }
        });
        
        const nav = wrapper.get('nav');
        expect(nav.isVisible()).toBe(true);
        
        const span = nav.findAll('span');
        expect(span.length).toBe(3);
        expect(span[0].text()).toBe('...');
        expect(span[1].text()).toBe('50');
        expect(span[2].text()).toBe('');
        
        const a = nav.findAll('a');
        expect(a.length).toBe(12);
        expect(a[0].text()).toBe('');
        expect(a[1].text()).toBe('1');
        expect(a[2].text()).toBe('2');
        expect(a[3].text()).toBe('41');
        expect(a[4].text()).toBe('42');
        expect(a[5].text()).toBe('43');
        expect(a[6].text()).toBe('44');
        expect(a[7].text()).toBe('45');
        expect(a[8].text()).toBe('46');
        expect(a[9].text()).toBe('47');
        expect(a[10].text()).toBe('48');
        expect(a[11].text()).toBe('49');
    });
});
