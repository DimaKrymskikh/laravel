import { mount } from "@vue/test-utils";

import SimpleTextarea from '@/components/Elements/Form/Textarea/SimpleTextarea.vue';
import Spinner from '@/components/Svg/Spinner.vue';
import { app } from '@/Services/app';

const titleText = 'Поле ввода';
const nRows = '5';

const getWrapper = function(errorsMessage = '') {
    return mount(SimpleTextarea, {
            props: {
                errorsMessage,
                modelValue: '',
                handler: vi.fn()
            }
        });
};

describe("@/components/Elements/Form/Textarea/SimpleTextarea.vue", () => {
    beforeEach(() => {
        app.isRequest = false;
    });
    
    it("Монтирование компоненты SimpleTextarea (isRequest: false)", async () => {
        const wrapper = getWrapper();
        
        // Спиннер отсутствует
        expect(wrapper.findComponent(Spinner).exists()).toBe(false);
        
        // Существует текстовое поле
        const textarea = wrapper.get('textarea');
        expect(textarea.isVisible()).toBe(true);
        expect(textarea.attributes('rows')).toBe(nRows);
        
        // Текстовое поле редактируется
        expect(textarea.element.value).toBe('');
        textarea.setValue('Некоторое слово');
        expect(textarea.element.value).toBe('Некоторое слово');
        expect(wrapper.emitted()).toHaveProperty('update:modelValue');
        expect(wrapper.emitted('update:modelValue')[0][0]).toBe('Некоторое слово');
        
        expect(wrapper.vm.textarea).toStrictEqual(textarea.element);
        expect(wrapper.vm.textarea.value).toBe('Некоторое слово');
        
        // Запись об ошибке отсутствует
        const error = wrapper.find('.error');
        expect(error.exists()).toBe(false);
    });
    
    it("Монтирование компоненты SimpleTextarea (isRequest: true)", async () => {
        app.isRequest = true;
        
        const wrapper = getWrapper();
        
        // Спиннер присутствует
        const spinner = wrapper.findComponent(Spinner);
        expect(spinner.exists()).toBe(true);
        expect(spinner.props('styleSpinner')).toBe('h-6 fill-gray-700 text-gray-200');
        expect(spinner.classes()).toContain('spinner');
        
        // Текстовое поле заблокировано
        const textarea = wrapper.get('textarea');
        expect(textarea.isVisible()).toBe(true);
        expect(textarea.attributes('class')).toContain('disabled');
        expect(textarea.attributes('disabled')).toBe('');
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
});
