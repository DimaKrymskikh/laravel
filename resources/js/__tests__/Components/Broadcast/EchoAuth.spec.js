import '@/bootstrap';
import { mount } from "@vue/test-utils";

import EchoAuth from '@/Components/Broadcast/EchoAuth.vue';
import BroadcastBlock from '@/Components/Broadcast/BroadcastBlock.vue';

const user = {
    id: 77,
    is_admin: false,
    login: 'TestLogin'
};

const getWrapper = function() {
    return mount(EchoAuth, {
            props: {
                user
            }
        });
};

describe("@/Components/Broadcast/EchoAuth.vue", () => {
    it("Монтирование компоненты EchoAuth", () => {
        const wrapper = getWrapper();
        
        // В BroadcastBlock передаётся пропс
        const broadcastBlock = wrapper.getComponent(BroadcastBlock);
        expect(broadcastBlock.props('broadcastMessage')).toBe(wrapper.vm.broadcastMessage);
        expect(broadcastBlock.props('clearBroadcastMessage')).toBe(wrapper.vm.clearBroadcastMessage);
    });
    
    it("Функция clearBroadcastMessage очищает послание", () => {
        const wrapper = getWrapper();
        
        wrapper.vm.broadcastMessage = 'Некоторое послание';
        wrapper.vm.clearBroadcastMessage();
        expect(wrapper.vm.broadcastMessage).toBe('');
    });
});
