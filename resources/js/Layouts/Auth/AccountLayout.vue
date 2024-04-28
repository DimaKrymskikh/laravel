<script setup>
import { Link } from '@inertiajs/vue3';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import BreadCrumb from '@/Components/Elements/BreadCrumb.vue';
import PersonalDataBlock from '@/Components/Pages/Auth/Account/PersonalDataBlock.vue';
import AuthAccountTabs from '@/Components/Tabs/AuthAccountTabs.vue';

defineProps({
    user: Object | null,
    errors: Object | null,
    linksList: Array
});
</script>

<template>
    <AuthLayout :errors="errors" :user="user">
        <div class="flex">
            <div class="w-1/12 pr-4 mt-16">
                <AuthAccountTabs />
            </div>
            <div class="w-11/12">
                <BreadCrumb :linksList="linksList" />
                <div class="relative">
                    <PersonalDataBlock :user="user" />
                    <h1>Добрый день, {{ user.login }}</h1>

                    <slot />
                </div>
            </div>
        </div>
    </AuthLayout>
</template>
