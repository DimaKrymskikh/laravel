import '@/bootstrap';
import { mount } from "@vue/test-utils";

import EchoUserFilm from '@/Components/Broadcast/EchoUserFilm.vue';
import BroadcastBlock from '@/Components/Broadcast/BroadcastBlock.vue';

const user = {
    id: 77,
    is_admin: false,
    login: 'TestLogin'
};

const getWrapper = function() {
    return mount(EchoUserFilm, {
            props: {
                user
            }
        });
};

describe("@/Components/Broadcast/EchoUserFilm.vue", () => {
    it("Монтирование компоненты EchoUserFilm", () => {
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
