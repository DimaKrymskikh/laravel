<script setup>
import AccountRemoveBlock from '@/Components/Pages/Auth/Account/PersonalDataBlock/PersonalData/AccountRemoveBlock.vue';
import AdminBlock from '@/Components/Pages/Auth/Account/PersonalDataBlock/PersonalData/AdminBlock.vue';
import EmailBlock from '@/Components/Pages/Auth/Account/PersonalDataBlock/PersonalData/EmailBlock.vue';
import TokenBlock from '@/Components/Pages/Auth/Account/PersonalDataBlock/PersonalData/TokenBlock.vue';
    
defineProps({
    user: Object | null
});
</script>

<template>
    <div class="w-80 h-screen bg-white p-4 shadow shadow-gray-500">
        <EmailBlock :user="user" />
        <TokenBlock />
        <AdminBlock :user="user" />
        <AccountRemoveBlock />
    </div>
</template>
