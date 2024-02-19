<script setup>
import { inject, ref, onMounted, onUnmounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import HouseSvg from '@/Components/Svg/HouseSvg.vue';
import ForbiddenModal from '@/components/Modal/ForbiddenModal.vue';
import GlobalModal from '@/components/Modal/GlobalModal.vue';
import AdminContentTabs from '@/components/Tabs/AdminContentTabs.vue';

defineProps({
    errors: Object
});

const filmsAccount = inject('filmsAccount');
const app = inject('app');

onMounted(() => {
    document.addEventListener('inertia:start', app.handlerStart);
    document.addEventListener('inertia:finish', app.handlerFinish);
});

onUnmounted(() => {
    document.removeEventListener('inertia:start', app.handlerStart);
    document.removeEventListener('inertia:finish', app.handlerFinish);
});    
</script>

<template>
    <nav id="main-nav" class="bg-orange-300 shadow shadow-orange-200">
        <div class="lg:container flex justify-between">
            <ul class="flex flex-row pt-2">
                <li class="nav-tab">
                    <Link class="nav-link self-center" href="/admin" :class="{ 'router-link-active': $page.component === 'Admin/Home' }">
                        <HouseSvg />
                    </Link>
                </li>
                <AdminContentTabs />
                <li class="nav-tab">
                    <Link
                        class="nav-link small-caps"
                        :href="filmsAccount.getUrl('/userfilms')"
                    >лк</Link>
                </li>
            </ul>
        </div>
    </nav>

    <main class="lg:container">
        <slot />
    </main>
    
    <ForbiddenModal />
    <GlobalModal v-if="app.isGlobalRequest && !app.isRequest" />
</template>
