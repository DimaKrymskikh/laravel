import { mount } from "@vue/test-utils";

import Checkbox from '@/components/Elements/Form/Checkbox.vue';
import { app } from '@/Services/app';
import * as testCheckbox from '@/__tests__/methods/Checkbox/checkbox';

const getWrapper = function(modelValue) {
    return mount(Checkbox, {
            props: {
                titleText: 'Поле ввода',
                modelValue
            }
        });
};

describe("@/components/Elements/Form/Checkbox", () => {
    beforeEach(() => {
        app.isRequest = false;
    });
    
    it("Проверка текста label", () => {
        const wrapper = getWrapper(false);

        const label = wrapper.get('label');
        expect(label.get('span').text()).toBe('Поле ввода');
    });
    
    it("Монтирование компоненты Checkbox (isRequest: false)", async () => {
        const wrapper = getWrapper(false);
        
        testCheckbox.successChecked(wrapper);
    });
    
    it("Монтирование компоненты Checkbox (isRequest: true)", async () => {
        app.isRequest = true;
        const wrapper = getWrapper(false);
        
        testCheckbox.failChecked(wrapper);
    });
});
