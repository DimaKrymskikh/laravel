<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import BroadcastBlock from '@/Components/Broadcast/BroadcastBlock.vue';

const { user, action, events } = defineProps({
    user: Object | null,
    action: Function | null,
    events: Array
});

const broadcastMessage = ref('');
    
const clearBroadcastMessage = function() {
    broadcastMessage.value = '';
};

const echoChannel = Echo.private(`auth.${user.id}`);

onMounted(() => {
    // Добавляем прослушиватели каналу echoChannel
    events.forEach((event) => {
        switch(event) {
            case 'AddFilmInUserList':
            case 'RemoveFilmFromUserList':
            case 'AddCityInWeatherList':
            case 'RemoveCityFromWeatherList':
                echoChannel
                    .listen(event, (e) => {
                        broadcastMessage.value = e.message;
                    });
                break;
            case 'RefreshCityWeather':
                echoChannel
                    .listen(event, (e) => {
                        action(e.weather);
                    });
                break;
        }
    });
});
    
onUnmounted(() => {
    // Удаляем прослушиватели канала echoChannel
    events.forEach((event) => {
        echoChannel.stopListening(event);
    });
});
</script>

<template>
    <BroadcastBlock
        :broadcastMessage="broadcastMessage"
        :clearBroadcastMessage="clearBroadcastMessage"
    />
</template>
