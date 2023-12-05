<script setup>
import { inject, ref } from 'vue';
import { Link } from '@inertiajs/vue3';
    
const isShowContent = ref(false);
const toggleContent = function() {
    isShowContent.value = !isShowContent.value;
};

</script>

<template>
    <li class="nav-tab relative z-50">
        <span
            class="nav-link small-caps cursor-pointer"
            :class="{ 'router-link-active': $page.component ===  'Admin/Cities' }"
            @click="toggleContent"
        >
            контент
        </span>
        <ul
            class="absolute whitespace-nowrap top-9 border-x border-b"
            v-if="isShowContent"
        >
            <li class="tabs-li">
                <Link
                    class="block px-4 py-1"
                    :class="{ 'tabs-link-active': $page.component === 'Admin/Cities' }"
                    href="/admin/cities"
                >
                    города
                </Link>
            </li>
            <li class="tabs-li">
                <Link
                    class="block px-4 py-1"
                    :class="{ 'tabs-link-active': $page.component === 'Admin/Languages' }"
                    href="/admin/languages"
                >
                    языки
                </Link>
            </li>
        </ul>
    </li>
</template>

