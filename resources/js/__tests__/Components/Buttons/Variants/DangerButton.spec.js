import { mount } from "@vue/test-utils";

import DangerButton from '@/Components/Buttons/Variants/DangerButton.vue';

describe("@/Components/Buttons/Variants/DangerButton.vue", () => {
    it("Монтирование компоненты DangerButton", async () => {
        const handler = vi.fn();
        
        const wrapper = mount(DangerButton, {
            props: {
                buttonText: 'Опасность',
                handler
            }
        });
        
        expect(wrapper.props('buttonText')).toBe('Опасность');
        expect(wrapper.props('handler')).toBe(handler);
        
        const button = wrapper.get('button');
        expect(button.classes()).toContain('px-4');
        expect(button.classes()).toContain('py-2');
        expect(button.classes()).toContain('bg-red-500');
        expect(button.classes()).toContain('text-white');
        expect(button.classes()).toContain('hover:bg-red-600');
        expect(button.classes()).toContain('rounded-lg');
        expect(button.text()).toBe('Опасность');
        
        expect(handler).not.toHaveBeenCalled();
        await button.trigger('click');
        expect(handler).toHaveBeenCalledTimes(1);
    });
});
