<script setup>
import { inject } from 'vue';
import { Link } from '@inertiajs/vue3';
import HouseSvg from '@/Components/Svg/HouseSvg.vue';
import ForbiddenModal from '@/components/Modal/ForbiddenModal.vue';
import AuthContentTabs from '@/components/Tabs/AuthContentTabs.vue';

defineProps({
    user: Object | null,
    errors: Object | null
});

const filmsAccount = inject('filmsAccount');
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
                    <Link class="nav-link small-caps" href="/logout">выход</Link>
                </li>
            </ul>
        </div>
    </nav>

    <main class="lg:container">
        <slot />
    </main>
    
    <ForbiddenModal />
</template>
