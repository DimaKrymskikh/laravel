<script setup>
import { ref, onUpdated } from 'vue';
import BroadcastModal from '@/components/Modal/BroadcastModal.vue';

const props = defineProps({
    broadcastMessage: String,
    clearBroadcastMessage: Function
});
    
const isShowBroadcastModal = ref(!!props.broadcastMessage);

onUpdated(() => {
    isShowBroadcastModal.value = !!props.broadcastMessage;
});
</script>

<template>
    <BroadcastModal
        :message="broadcastMessage"
        @clear-broadcast-message="clearBroadcastMessage"
        v-if="isShowBroadcastModal" 
    />
</template>
