import { mount } from "@vue/test-utils";

import SimpleInput from '@/components/Elements/Form/Input/SimpleInput.vue';
import Spinner from '@/components/Svg/Spinner.vue';
import { app } from '@/Services/app';

const titleText = 'Поле ввода';
const nRows = '5';

const getWrapper = function(errorsMessage = '') {
    return mount(SimpleInput, {
            props: {
                errorsMessage,
                modelValue: '',
                hide: vi.fn(),
                handler: vi.fn()
            }
        });
};

describe("@/components/Elements/Form/Input/SimpleInput.vue", () => {
    beforeEach(() => {
        app.isRequest = false;
    });
    
    it("Монтирование компоненты SimpleInput (isRequest: false)", async () => {
        const wrapper = getWrapper();
        
        // Спиннер отсутствует
        expect(wrapper.findComponent(Spinner).exists()).toBe(false);
        
        // Существует текстовое поле
        const input = wrapper.get('input');
        expect(input.isVisible()).toBe(true);
        
        // Текстовое поле редактируется
        expect(input.element.value).toBe('');
        input.setValue('Некоторое слово');
        expect(input.element.value).toBe('Некоторое слово');
        expect(wrapper.emitted()).toHaveProperty('update:modelValue');
        expect(wrapper.emitted('update:modelValue')[0][0]).toBe('Некоторое слово');
        
        expect(wrapper.vm.input).toStrictEqual(input.element);
        expect(wrapper.vm.input.value).toBe('Некоторое слово');
        
        // Запись об ошибке отсутствует
        const error = wrapper.find('.error');
        expect(error.exists()).toBe(false);
    });
    
    it("Монтирование компоненты SimpleInput (isRequest: true)", async () => {
        app.isRequest = true;
        
        const wrapper = getWrapper();
        
        // Спиннер присутствует
        const spinner = wrapper.findComponent(Spinner);
        expect(spinner.exists()).toBe(true);
        expect(spinner.props('styleSpinner')).toBe('h-6 fill-gray-700 text-gray-200');
        expect(spinner.classes()).toContain('spinner');
        
        // Текстовое поле заблокировано
        const input = wrapper.get('input');
        expect(input.isVisible()).toBe(true);
        expect(input.attributes('class')).toContain('disabled');
        expect(input.attributes('disabled')).toBe('');
        expect(wrapper.emitted()).not.toHaveProperty('update:modelValue');
        
        // Запись об ошибке отсутствует
        const error = wrapper.find('.error');
        expect(error.exists()).toBe(false);
    });
    
    it("Отрисовка ошибки", async () => {
        const wrapper = getWrapper('Некоторая ошибка');
        
        // Отрисовывается сообщение об ошибке
        const error = wrapper.find('.error');
        expect(error.exists()).toBe(true);
        expect(error.text()).toBe('Некоторая ошибка');
    });
    
    it("Событие blur", async () => {
        const wrapper = getWrapper(false);
        const input = wrapper.find('input');
        
        await input.trigger('blur');
        expect(wrapper.vm.props.hide).toHaveBeenCalledTimes(1);
        expect(wrapper.vm.props.handler).not.toHaveBeenCalled();
    });
    
    it("Событие change", async () => {
        const wrapper = getWrapper(false);
        const input = wrapper.find('input');
        
        await input.trigger('change');
        expect(wrapper.vm.props.hide).not.toHaveBeenCalled();
        expect(wrapper.vm.props.handler).toHaveBeenCalledTimes(1);
    });
});
