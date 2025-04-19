import { mount } from "@vue/test-utils";

import YearDropdown from '@/Components/Elements/Dropdowns/YearDropdown.vue';

const getWrapper = function(releaseYear) {
    return mount(YearDropdown, {
            props: {
                releaseYear,
                setNewReleaseYear: vi.fn()
            }
        });
};


describe("@/Components/Elements/Dropdowns/YearDropdown.vue", () => {
    it("Монтирование компоненты, выпадение/сокрытие списка", async () => {
        const wrapper = getWrapper('');
        
        const button = wrapper.get('button');
        expect(button.text()).toBe('все');
        // Список скрыт
        expect(wrapper.find('ul').exists()).toBe(false);
        
        // Кликаем по кнопке первый раз
        await button.trigger('click');
        // Появился список
        const ul =  wrapper.find('ul');
        expect(ul.exists()).toBe(true);
        const li = ul.findAll('li');
        expect(li).toHaveLength(201);
        // Первый вариант выбран
        expect(li[0].text()).toBe('все');
        expect(li[0].classes('cursor-not-allowed')).toBe(true);
        // Проверка одного элемента
        expect(li[77].text()).toBe('1977');
        expect(li[77].classes('cursor-not-allowed')).toBe(false);
        
        // Второй раз кликаем по кнопке
        await button.trigger('click');
        // Список исчезает
        expect(wrapper.find('ul').exists()).toBe(false);
        
        // Кликаем по кнопке третий раз
        await button.trigger('click');
        // Появился список языков
        expect(wrapper.find('ul').exists()).toBe(true);
    });
    
    it("Изменение года", async () => {
        const wrapper = getWrapper('1977');
        
        const button = wrapper.get('button');
        // На кнопке год, который задан в начальный момент
        expect(button.text()).toBe('1977');
        // Список скрыт
        expect(wrapper.find('ul').exists()).toBe(false);
        
        // Кликаем по кнопке первый раз
        await button.trigger('click');
        // Появился список
        const ul =  wrapper.find('ul');
        expect(ul.exists()).toBe(true);
        const li = ul.findAll('li');
        expect(li).toHaveLength(201);
        // Первый вариант не выбран
        expect(li[0].text()).toBe('все');
        expect(li[0].classes('cursor-not-allowed')).toBe(false);
        // Активный год
        expect(li[77].text()).toBe('1977');
        expect(li[77].classes('cursor-not-allowed')).toBe(true);
        
        const setNewReleaseYear = vi.spyOn(wrapper.vm.props, 'setNewReleaseYear');
        expect(setNewReleaseYear).not.toHaveBeenCalled();
        
        // Кликаем по году 2020
        await li[120].trigger('click');
        expect(setNewReleaseYear).toHaveBeenCalledTimes(1);
        expect(setNewReleaseYear).toHaveBeenCalledWith('2020');
        // Список исчезает
        expect(wrapper.find('ul').exists()).toBe(false);
        
        // Кликаем по кнопке второй раз
        await button.trigger('click');
        // Первый вариант не выбран
        expect(li[0].text()).toBe('все');
        expect(li[0].classes('cursor-not-allowed')).toBe(false);
        
        // Кликаем по первому варианту (все)
        await li[0].trigger('click');
        expect(setNewReleaseYear).toHaveBeenCalledTimes(2);
        expect(setNewReleaseYear).toHaveBeenCalledWith('');
        // Список исчезает
        expect(wrapper.find('ul').exists()).toBe(false);
    });
});
