import { mount } from "@vue/test-utils";

import BroadcastModal from '@/components/Modal/BroadcastModal.vue';

const message = 'Некоторое послание';

describe("@/components/Modal/BroadcastModal.vue", () => {
    
    it("Монтирование компоненты BroadcastModal", () => {
        const wrapper = mount(BroadcastModal, {
            props: {
                message
            }
        });

        expect(wrapper.text()).toContain(message);
        
        // Отображается кнопка 'Закрыть'
        const button = wrapper.get('button');
        expect(button.text()).toBe('Закрыть');
        // Клик по кнопке порождает событие clearBroadcastMessage
        button.trigger('click');
        expect(wrapper.emitted()).toHaveProperty('clearBroadcastMessage');
    });
});
