<script setup>
import { ref } from 'vue';
import PersonalData from '@/Components/Pages/Auth/Account/PersonalDataBlock/PersonalData.vue';
import Bars3 from '@/Components/Svg/Bars3.vue';
    
defineProps({
    user: Object | null
});

const isPersonalData = ref(false);

const togglePersonalData = function() {
    isPersonalData.value = !isPersonalData.value;
};

</script>

<template>
    <div class="absolute right-0">
        <div class="flex justify-end">
            <Bars3 class="cursor-pointer" @click="togglePersonalData" />
        </div>
        <PersonalData :user="user" v-if="isPersonalData" />
    </div>
</template>
