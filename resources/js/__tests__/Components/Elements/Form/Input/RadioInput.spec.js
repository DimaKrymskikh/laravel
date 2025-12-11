import { mount } from "@vue/test-utils";

import RadioInput from '@/components/Elements/Form/Input/RadioInput.vue';
import { app } from '@/Services/app';

const titleText = 'Текст метки';
const radioValue = '15';
const isChecked = false;

const getWrapper = function() {
    return mount(RadioInput, {
            props: {
                titleText,
                isChecked,
                radioValue,
                modelValue: ''
            }
        });
};

const checkInput = function(input) {
    expect(input.isVisible()).toBe(true);
    expect(input.attributes('type')).toBe('radio');
    expect(input.element.checked).toBe(isChecked);
};

describe("@/components/Elements/Form/Input/RadioInput.vue", () => {
    beforeEach(() => {
        app.isRequest = false;
    });
    
    it("Монтирование компоненты RadioInput (isRequest: false)", async () => {
        const wrapper = getWrapper();
        
        const input = wrapper.get('input');
        checkInput(input);
        expect(input.attributes('disabled')).toBe(undefined);
        
        await input.trigger('click');
        expect(wrapper.emitted()).toHaveProperty('update:modelValue');
        expect(wrapper.emitted('update:modelValue')[0][0]).toBe(radioValue);
    });
    
    it("Монтирование компоненты RadioInput (isRequest: true)", async () => {
        app.isRequest = true;
        
        const wrapper = getWrapper();
        
        // Текстовое поле заблокировано
        const input = wrapper.get('input');
        checkInput(input);
        expect(input.attributes('disabled')).toBe('');
        
        await input.trigger('click');
        expect(wrapper.emitted()).not.toHaveProperty('update:modelValue');
    });
});
