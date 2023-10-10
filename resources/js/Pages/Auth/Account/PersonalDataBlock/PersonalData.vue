<script setup>
import EmailBlock from '@/Pages/Auth/Account/PersonalDataBlock/PersonalData/EmailBlock.vue';
import TokenBlock from '@/Pages/Auth/Account/PersonalDataBlock/PersonalData/TokenBlock.vue';
    
defineProps({
    user: Object | null
});
</script>

<template>
    <div class="w-80 h-screen bg-white p-4 shadow shadow-gray-500">
        <EmailBlock :user="user" />
        <TokenBlock />
    </div>
</template>
