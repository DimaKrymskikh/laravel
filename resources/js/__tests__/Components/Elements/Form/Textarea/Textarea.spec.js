import { mount } from "@vue/test-utils";

import Textarea from '@/components/Elements/Form/Textarea/Textarea.vue';
import Spinner from '@/components/Svg/Spinner.vue';
import { app } from '@/Services/app';

const titleText = 'Поле ввода';
const nRows = '5';

const getWrapper = function(isTextareaDisabled = undefined, errorsMessage = '') {
    return mount(Textarea, {
            props: {
                titleText,
                isTextareaDisabled,
                isTextareaAutofocus: false,
                errorsMessage,
                modelValue: ''
            }
        });
};

describe("@/components/Elements/Form/Textarea/Textarea.vue", () => {
    beforeEach(() => {
        app.isRequest = false;
    });
    
    it("Монтирование компоненты Textarea (isRequest: false)", async () => {
        const wrapper = getWrapper();

        // Метка поля содержит нужный текст
        const label = wrapper.get('label');
        expect(label.get('span').text()).toBe(titleText);
        
        // Спиннер отсутствует
        expect(label.findComponent(Spinner).exists()).toBe(false);
        
        // Существует текстовое поле
        const textarea = label.get('textarea');
        expect(textarea.isVisible()).toBe(true);
        expect(textarea.attributes('rows')).toBe(nRows);
        
        // Текстовое поле редактируется
        expect(textarea.element.value).toBe('');
        textarea.setValue('Некоторое слово');
        expect(textarea.element.value).toBe('Некоторое слово');
        expect(wrapper.emitted()).toHaveProperty('update:modelValue');
        expect(wrapper.emitted('update:modelValue')[0][0]).toBe('Некоторое слово');
        
        expect(wrapper.vm.textarea).toStrictEqual(textarea.element);
        expect(wrapper.vm.props.isTextareaAutofocus).toBe(false);
        expect(wrapper.vm.textarea.value).toBe('Некоторое слово');
        
        // Запись об ошибке отсутствует
        const error = wrapper.find('.error');
        expect(error.exists()).toBe(false);
    });
    
    it("Монтирование компоненты Textarea (isRequest: true)", async () => {
        app.isRequest = true;
        
        const wrapper = getWrapper();

        // Метка поля содержит нужный текст
        const label = wrapper.get('label');
        expect(label.get('span').text()).toBe(titleText);
        
        // Спиннер присутствует
        const spinner = label.findComponent(Spinner);
        expect(spinner.exists()).toBe(true);
        expect(spinner.props('styleSpinner')).toBe('h-6 fill-gray-700 text-gray-200');
        expect(spinner.classes()).toContain('spinner');
        
        // Текстовое поле заблокировано
        const textarea = label.get('textarea');
        expect(textarea.isVisible()).toBe(true);
        expect(textarea.attributes('class')).toContain('disabled');
        expect(textarea.attributes('disabled')).toBe('');
        expect(wrapper.emitted()).not.toHaveProperty('update:modelValue');
        
        // Запись об ошибке отсутствует
        const error = wrapper.find('.error');
        expect(error.exists()).toBe(false);
    });
    
    it("Отрисовка ошибки", async () => {
        const wrapper = getWrapper(undefined, 'Некоторая ошибка');
        
        // Отрисовывается сообщение об ошибке
        const error = wrapper.find('.error');
        expect(error.exists()).toBe(true);
        expect(error.text()).toBe('Некоторая ошибка');
    });
    
    it("Отрисовка Textarea (isRequest: false, isTextareaDisabled: true)", async () => {
        const wrapper = getWrapper(true);
        
        const textarea = wrapper.get('textarea');
        expect(textarea.classes()).toContain('cursor-not-allowed');
    });
});
