<script setup>
import { ref } from 'vue';
import BroadcastBlock from '@/Components/Broadcast/BroadcastBlock.vue';

const { user } = defineProps({
    user: Object | null
});

const broadcastMessage = ref('');
    
const clearBroadcastMessage = function() {
    broadcastMessage.value = '';
};

Echo.private(`auth.${user.id}`)
    .listen('AddFilmInUserList', (e) => {
        broadcastMessage.value = e.message;
    })
    .listen('RemoveFilmFromUserList', (e) => {
        broadcastMessage.value = e.message;
    })
    .listen('AddCityInWeatherList', (e) => {
        broadcastMessage.value = e.message;
    })
    .listen('RemoveCityFromWeatherList', (e) => {
        broadcastMessage.value = e.message;
    });
</script>

<template>
    <BroadcastBlock
        :broadcastMessage="broadcastMessage"
        :clearBroadcastMessage="clearBroadcastMessage"
    />
</template>
