<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    linksList: Array
});
</script>

<template>
    <nav class="mb-4">
        <ol class="flex">
            <template v-for="item in linksList">
                <li class="text-gray-400 py-2" v-if="item.link === undefined">
                    {{item.text}}
                </li>
                <li class="text-orange-700 py-2 hover:text-orange-900 after:content-['/'] after:px-2 after:text-orange-900" v-else>
                    <Link :href="item.link">{{item.text}}</Link>
                </li>
            </template>
        </ol>
    </nav>
</template>
