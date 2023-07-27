<script setup>
import { inject, ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import HouseSvg from '@/Components/Svg/HouseSvg.vue';
import ForbiddenModal from '@/components/Modal/ForbiddenModal.vue';

defineProps({
    errors: Object | null
});

const message = ref('');

const filmsAccount = inject('filmsAccount');
</script>

<template>
    <nav id="main-nav" class="bg-orange-300 shadow shadow-orange-200">
        <div class="lg:container flex justify-between">
            <ul class="flex flex-row pt-2">
                <li class="nav-tab">
                    <Link class="nav-link self-center" href="/admin" :class="{ 'router-link-active': $page.component === 'Auth/Home' }">
                        <HouseSvg />
                    </Link>
                </li>
                <li class="nav-tab">
                    <Link
                        class="nav-link small-caps"
                        href="/cities"
                        :class="{ 'router-link-active': $page.component === 'Admin/Cities' }"
                    >города</Link>
                </li>
                <li class="nav-tab">
                    <Link
                        class="nav-link small-caps"
                        :href="filmsAccount.getUrl()"
                    >лк</Link>
                </li>
            </ul>
            <span class="text-orange-700 py-2">{{ message }}</span>
        </div>
    </nav>

    <main class="lg:container">
        <slot />
    </main>
    
    <ForbiddenModal :errors="errors" />
</template>

