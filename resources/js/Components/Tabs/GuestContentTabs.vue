<script setup>
import { inject, ref } from 'vue';
import { Link } from '@inertiajs/vue3';
    
const isShowContent = ref(false);
const toggleContent = function() {
    isShowContent.value = !isShowContent.value;
};

const filmsList = inject('filmsList');
</script>

<template>
    <li class="nav-tab relative z-50">
        <span
            class="nav-link small-caps cursor-pointer"
            :class="{ 'router-link-active': $page.component === 'Guest/Films' || $page.component === 'Guest/Cities' }"
            @click="toggleContent"
        >
            контент
        </span>
        <ul
            class="absolute whitespace-nowrap top-9 border-x border-b"
            v-if="isShowContent"
            @click="toggleContent"
        >
            <li class="tabs-li">
                <Link
                    class="block px-4 py-1"
                    :class="{ 'tabs-link-active': $page.component === 'Guest/Films' }"
                    :href="filmsList.getUrl('/guest/films')"
                >
                    фильмы
                </Link>
            </li>
            <li class="tabs-li">
                <Link
                    class="block px-4 py-1"
                    :class="{ 'tabs-link-active': $page.component === 'Guest/Cities' }"
                    href="/guest/cities"
                >
                    города
                </Link>
            </li>
        </ul>
    </li>
</template>

