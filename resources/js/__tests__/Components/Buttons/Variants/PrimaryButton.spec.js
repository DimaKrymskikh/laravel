import { mount } from "@vue/test-utils";

import PrimaryButton from '@/Components/Buttons/Variants/PrimaryButton.vue';

describe("@/Components/Buttons/Variants/PrimaryButton.vue", () => {
    it("Монтирование компоненты PrimaryButton", async () => {
        const handler = vi.fn();
        
        const wrapper = mount(PrimaryButton, {
            props: {
                buttonText: 'Информация',
                handler
            }
        });
        
        expect(wrapper.props('buttonText')).toBe('Информация');
        expect(wrapper.props('handler')).toBe(handler);
        
        const button = wrapper.get('button');
        expect(button.classes()).toContain('px-4');
        expect(button.classes()).toContain('py-2');
        expect(button.classes()).toContain('bg-blue-500');
        expect(button.classes()).toContain('text-white');
        expect(button.classes()).toContain('hover:bg-blue-600');
        expect(button.classes()).toContain('rounded-lg');
        expect(button.text()).toBe('Информация');
        
        expect(handler).not.toHaveBeenCalled();
        await button.trigger('click');
        expect(handler).toHaveBeenCalledTimes(1);
    });
});
