import { mount } from "@vue/test-utils";

import { Link } from '@inertiajs/vue3'
import { statisticsOptions } from '@/Services/Content/Weather/weatherStatistics';
import LinkBlock from '@/Components/Pages/Auth/Account/Weather/weatherStatistics/LinkBlock.vue';

import { city } from '@/__tests__/data/cities';

const getWrapper = function() {
    return mount(LinkBlock, {
            props: {
                city
            }
        });
};

describe("@/Components/Pages/Auth/Account/Weather/weatherStatistics/LinkBlock.vue", () => {
    beforeEach(() => {
        statisticsOptions.datefrom = '';
        statisticsOptions.dateto = '';
        statisticsOptions.interval = '';
    });
    
    it("Отрисовка LinkBlock (параметры не заданы)", async () => {
        const wrapper = getWrapper();
        expect(wrapper.findComponent(Link).exists()).toBe(false);
        expect(wrapper.text()).toContain('Не задано начало промежутка.');
        expect(wrapper.text()).toContain('Не задан конец промежутка.');
        expect(wrapper.text()).toContain('Не задан интервал.');
    });
    
    it("Отрисовка LinkBlock (параметры заданы)", async () => {
        statisticsOptions.datefrom = '01.01.2025';
        statisticsOptions.dateto = '31.01.2025';
        statisticsOptions.interval = 'week';
        
        const wrapper = getWrapper();
        expect(wrapper.findComponent(Link).exists()).toBe(true);
        expect(wrapper.text()).not.toContain('Не задано начало промежутка.');
        expect(wrapper.text()).not.toContain('Не задан конец промежутка.');
        expect(wrapper.text()).not.toContain('Не задан интервал.');
    });
});
