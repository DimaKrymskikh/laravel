import { mount } from "@vue/test-utils";

import Checkbox from '@/components/Elements/Form/Checkbox.vue';
import { app } from '@/Services/app';
import * as testCheckbox from '@/__tests__/methods/Checkbox/checkbox';

const getWrapper = function(modelValue) {
    return mount(Checkbox, {
            props: {
                titleText: 'Поле ввода',
                modelValue,
                hide: vi.fn(),
                handler: vi.fn()
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
