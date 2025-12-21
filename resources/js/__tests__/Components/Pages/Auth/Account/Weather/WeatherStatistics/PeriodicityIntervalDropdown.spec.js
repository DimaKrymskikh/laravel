import { mount } from "@vue/test-utils";

import PeriodicityIntervalDropdown from '@/Components/Pages/Auth/Account/Weather/WeatherStatistics/PeriodicityIntervalDropdown.vue';
import { statisticsOptions, periodicityIntervals } from '@/Services/Content/Weather/weatherStatistics';

const getWrapper = function(changeNumber) {
    return mount(Dropdown, {
            props: {
                buttonName: 'Текст кнопки',
                itemsNumberOnPage: 50,
                changeNumber
            }
        });
};

describe("@/Components/Pages/Auth/Account/Weather/WeatherStatistics/PeriodicityIntervalDropdown.vue", () => {
    it("Монтирование компоненты, выпадение/сокрытие списка", async () => {
        const wrapper = mount(PeriodicityIntervalDropdown);

        const button = wrapper.get('button');
        // Список скрыт
        expect(wrapper.find('ul').exists()).toBe(false);
        
        await button.trigger('click');
        
        // Появился список
        const ul =  wrapper.find('ul');
        expect(ul.exists()).toBe(true);
        const li = ul.findAll('li');
        expect(li).toHaveLength(periodicityIntervals.length);
        
        // Второй раз кликаем по кнопке
        await button.trigger('click');
        // Список исчезает
        expect(wrapper.find('ul').exists()).toBe(false);
    });
    
    it("Изменение выбора", async () => {
        const wrapper = mount(PeriodicityIntervalDropdown);

        // Список скрыт
        expect(wrapper.find('ul').exists()).toBe(false);
        
        // Кликаем по кнопке
        const button = wrapper.get('button');
        await button.trigger('click');
        
        // Появился список 
        let ul =  wrapper.find('ul');
        let lis = ul.findAll('li');
        for(const index in lis) {
            expect(lis[index].text()).toBe(statisticsOptions.getIntervalText(periodicityIntervals[index]));
        }
        
        // Интервал ещё не выбран
        expect(statisticsOptions.interval).toBe('');
        expect(button.text()).toContain('не выбран');
        
        // Выбираем некий интервал
        await lis[2].trigger('click');
        // Список скрыт
        expect(wrapper.find('ul').exists()).toBe(false);
        // Интервал выбран
        expect(statisticsOptions.interval).toBe(periodicityIntervals[2]);
        expect(button.text()).toContain(statisticsOptions.getIntervalText(periodicityIntervals[2]));
        
        // Снова открываем список
        await button.trigger('click');
        ul =  wrapper.find('ul');
        lis = ul.findAll('li');
        
        // Кликаем ещё раз по тому же выбору
        await lis[2].trigger('click');
        // Список по-прежднему виден
        expect(wrapper.find('ul').exists()).toBe(true);
        // Интервал не изменился
        expect(statisticsOptions.interval).toBe(periodicityIntervals[2]);
        expect(button.text()).toContain(statisticsOptions.getIntervalText(periodicityIntervals[2]));
        
        // Выбираем другой интервал
        await lis[0].trigger('click');
        // Список скрыт
        expect(wrapper.find('ul').exists()).toBe(false);
        // Интервал изменился
        expect(statisticsOptions.interval).toBe(periodicityIntervals[0]);
        expect(button.text()).toContain(statisticsOptions.getIntervalText(periodicityIntervals[0]));
    });
});
