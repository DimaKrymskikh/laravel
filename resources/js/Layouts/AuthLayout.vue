<script setup>
import { inject, onMounted, onUnmounted } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import HouseSvg from '@/Components/Svg/HouseSvg.vue';
import ForbiddenModal from '@/components/Modal/ForbiddenModal.vue';
import GlobalModal from '@/components/Modal/GlobalModal.vue';
import AuthContentTabs from '@/components/Tabs/AuthContentTabs.vue';

defineProps({
    user: Object | null,
    errors: Object | null
});

const filmsAccount = inject('filmsAccount');
const app = inject('app');

const form = useForm({});

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
                    <Link class="nav-link self-center" href="/" :class="{ 'router-link-active': $page.component === 'Auth/Home' }">
                        <HouseSvg />
                    </Link>
                </li>
                <AuthContentTabs />
                <li class="nav-tab">
                    <Link
                        class="nav-link small-caps"
                        :href="filmsAccount.getUrl('/userfilms')"
                        :class="{ 'router-link-active': $page.component === 'Auth/Account/UserFilms' }"
                    >лк</Link>
                </li>
                <li class="nav-tab" v-if="user.is_admin">
                    <Link class="nav-link small-caps" href="/admin">
                        администрирование
                    </Link>
                </li>
                <li class="nav-tab">
                    <form @submit.prevent="form.post('/logout')">
                        <button class="nav-link small-caps">выход</button>
                    </form>
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
