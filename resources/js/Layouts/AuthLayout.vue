<script setup>
import { Link } from '@inertiajs/vue3';
import HouseSvg from '@/Components/Svg/HouseSvg.vue';
import ForbiddenModal from '@/components/Modal/ForbiddenModal.vue';

defineProps({
    errors: Object | null
});
</script>

<template>
    <nav id="main-nav" class="bg-orange-300 shadow shadow-orange-200">
        <div class="lg:container">
            <ul class="flex flex-row">
                <li class="px-2 h-12 flex">
                    <Link class="self-center" href="/">
                        <HouseSvg />
                    </Link>
                </li>
                <li class="nav-tab">
                    <Link class="nav-link small-caps" href="/catalog">каталог</Link>
                </li>
                <li class="nav-tab">
                    <Link class="nav-link small-caps" href="/account">лк</Link>
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
    
    <ForbiddenModal :errors="errors" />
</template>
