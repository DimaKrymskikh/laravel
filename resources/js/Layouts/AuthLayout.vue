<script setup>
import { inject } from 'vue';
import { Link } from '@inertiajs/vue3';
import HouseSvg from '@/Components/Svg/HouseSvg.vue';
import ForbiddenModal from '@/components/Modal/ForbiddenModal.vue';

defineProps({
    errors: Object | null
});

const filmsCatalog = inject('filmsCatalog');
const filmsAccount = inject('filmsAccount');
</script>

<template>
    <nav id="main-nav" class="bg-orange-300 shadow shadow-orange-200">
        <div class="lg:container">
            <ul class="flex flex-row pt-2">
                <li class="nav-tab">
                    <Link class="nav-link self-center" href="/" :class="{ 'router-link-active': $page.component === 'Auth/Home' }">
                        <HouseSvg />
                    </Link>
                </li>
                <li class="nav-tab">
                    <Link
                        class="nav-link small-caps"
                        :href="filmsCatalog.getUrl()"
                        :class="{ 'router-link-active': $page.component === 'Auth/Catalog' }"
                    >каталог</Link>
                </li>
                <li class="nav-tab">
                    <Link
                        class="nav-link small-caps"
                        :href="filmsAccount.getUrl()"
                        :class="{ 'router-link-active': $page.component === 'Auth/Account' }"
                    >лк</Link>
                </li>
                <li class="nav-tab">
                    <Link class="nav-link small-caps" href="/logout" :class="{ 'router-link-active': $page.url === '/logout' }">выход</Link>
                </li>
            </ul>
        </div>
    </nav>

    <main class="lg:container">
        <slot />
    </main>
    
    <ForbiddenModal :errors="errors" />
</template>
