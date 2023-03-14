<script setup>
import { inject } from 'vue';
import { Link } from '@inertiajs/vue3';
import HouseSvg from '@/Components/Svg/HouseSvg.vue';
import ForbiddenModal from '@/components/Modal/ForbiddenModal.vue';

defineProps({
    errors: Object | null
});

const filmsCatalog = inject('filmsCatalog');
</script>

<template>
    <nav id="main-nav" class="bg-orange-300 shadow shadow-orange-200">
        <div class="lg:container">
            <ul class="flex flex-row pt-2">
                <li class="nav-tab">
                    <Link class="nav-link self-center" href="/" :class="{ 'router-link-active': $page.component === 'Guest/Home' }">
                        <HouseSvg />
                    </Link>
                </li>
                <li class="nav-tab">
                    <Link
                        class="nav-link small-caps"
                        :href="filmsCatalog.getUrl()"
                        :class="{ 'router-link-active': $page.component === 'Guest/Catalog' }"
                    >каталог</Link>
                </li>
                <li class="nav-tab">
                    <Link class="nav-link small-caps" href="/login" :class="{ 'router-link-active': $page.component === 'Guest/Login' }">вход</Link>
                </li>
            </ul>
        </div>
    </nav>

    <main class="lg:container">
        <slot />
    </main>
    
    <ForbiddenModal :errors="errors" />
</template>
