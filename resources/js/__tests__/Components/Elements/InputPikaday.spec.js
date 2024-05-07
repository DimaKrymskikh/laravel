import '@/bootstrap';
import { mount } from "@vue/test-utils";

import ImputPikaday from '@/Components/Elements/ImputPikaday.vue';

const getWrapper = function() {
    return mount(ImputPikaday, {
            props: {
                datepicker: 'from',
                modelValue: ''
            }
        });
};

describe("@/Components/Elements/ImputPikaday.vue", () => {
    it("Монтирование компоненты ImputPikaday", async () => {
        const pikaday = vi.spyOn(window, 'pikaday');
        expect(pikaday).toHaveBeenCalledTimes(0);
        
        const wrapper = getWrapper();
        // При монтировании вызывается функция window.pikaday
        expect(pikaday).toHaveBeenCalledTimes(1);

        const input = wrapper.get('input');
        expect(input.attributes('id')).toBe(wrapper.vm.props.datepicker);
        expect(input.attributes('type')).toBe('text');
        expect(input.classes('w-32')).toBe(true);
        expect(input.classes('font-sans')).toBe(true);
    });
});
