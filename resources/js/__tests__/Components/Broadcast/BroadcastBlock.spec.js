import { mount } from "@vue/test-utils";

import BroadcastBlock from '@/Components/Broadcast/BroadcastBlock.vue';
import BroadcastModal from '@/components/Modal/BroadcastModal.vue';

const broadcastMessage = 'Некоторое послание';

describe("@/Components/Broadcast/BroadcastBlock.vue", () => {
    it("Монтирование компоненты BroadcastBlock (передано сообщение)", () => {
        const clearBroadcastMessage = vi.fn();
        
        const wrapper = mount(BroadcastBlock, {
            props: {
                broadcastMessage,
                clearBroadcastMessage
            }
        });

        // В модальное окно передаётся сообщение
        const broadcastModal = wrapper.getComponent(BroadcastModal);
        expect(broadcastModal.props('message')).toBe(broadcastMessage);
        
        expect(clearBroadcastMessage).not.toHaveBeenCalled();
        // Событие 'clear-broadcast-message' вызывает функцию clearBroadcastMessage
        broadcastModal.trigger('clear-broadcast-message');
        expect(clearBroadcastMessage).toHaveBeenCalledTimes(1);
    });
    
    it("При получении сообщения появляется модальное окно", async () => {
        const wrapper = mount(BroadcastBlock, {
            props: {
                broadcastMessage: '',
                clearBroadcastMessage: vi.fn()
            }
        });

        // Модальное окно отсутствует
        expect(wrapper.findComponent(BroadcastModal).exists()).toBe(false);
        
        // При получении сообщения появляется модальное окно
        await wrapper.setProps({ broadcastMessage });
        expect(wrapper.findComponent(BroadcastModal).exists()).toBe(true);
    });
});
