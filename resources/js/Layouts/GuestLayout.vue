<script setup>
import { Link } from '@inertiajs/vue3';
import { app } from '@/Services/app';
import { useGlobalRequest } from '@/Services/inertia';
import HouseSvg from '@/Components/Svg/HouseSvg.vue';
import ForbiddenModal from '@/components/Modal/ForbiddenModal.vue';
import GlobalModal from '@/components/Modal/GlobalModal.vue';
import GuestContentTabs from '@/components/Tabs/GuestContentTabs.vue';

const isGlobalRequest = useGlobalRequest();
</script>

<template>
    <nav id="main-nav" class="bg-orange-300 shadow shadow-orange-200">
        <div class="lg:container">
            <ul class="flex flex-row pt-2">
                <li class="nav-tab">
                    <Link class="nav-link self-center" href="/guest" :class="{ 'router-link-active': $page.component === 'Guest/Home' }">
                        <HouseSvg />
                    </Link>
                </li>
                <GuestContentTabs />
                <li class="nav-tab">
                    <Link class="nav-link small-caps" href="/login" :class="{ 'router-link-active': $page.component === 'Guest/Login' }">вход</Link>
                </li>
            </ul>
        </div>
    </nav>

    <main class="lg:container">
        <slot />
    </main>
    
    <ForbiddenModal />
    <GlobalModal v-if="isGlobalRequest && !app.isRequest" />
</template>
