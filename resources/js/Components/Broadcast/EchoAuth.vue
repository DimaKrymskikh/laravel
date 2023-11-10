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

Echo.private(`film.${user.id}`)
    .listen('AddFilm', (e) => {
        broadcastMessage.value = e.message;
    })
    .listen('RemoveFilm', (e) => {
        broadcastMessage.value = e.message;
    });
</script>

<template>
    <BroadcastBlock
        :broadcastMessage="broadcastMessage"
        :clearBroadcastMessage="clearBroadcastMessage"
    />
</template>
