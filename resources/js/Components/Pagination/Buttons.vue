<script setup>
import { Link } from '@inertiajs/vue3';

const { films } = defineProps({
    films: Object
});

const filmsNumber = films.links.slice(1, -1);

</script>

<template>
    <nav class="font-sans py-4" v-if="films.last_page > 0">
        <div class="flex items-center justify-center">
            <span class="pagination-disabled inline-flex items-center text-sm font-medium rounded-l-md leading-5" v-if="films.links[1].active">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
            </span>
            <Link :href="films.links[0].url" class="pagination inline-flex items-center text-sm font-medium rounded-l-md leading-5 focus:z-10 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-500 transition ease-in-out duration-150" v-else>
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
            </Link>

            <template  v-for="element in filmsNumber">
                <Link 
                    :href="element.url" 
                    class="pagination inline-flex items-center text-sm font-medium leading-5 focus:z-10 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150"
                    v-if="element.url && !element.active"
                >
                    {{ element.label }}
                </Link>
                <span
                    class="pagination-active inline-flex items-center text-sm font-medium leading-5"
                    :class="element.active ? 'pagination-active' : 'pagination-disabled'"
                    v-else
                >
                    {{ element.label }}
                </span>
            </template>

            <span class="pagination-disabled inline-flex items-center text-sm font-medium rounded-r-md leading-5" v-if="films.links[films.links.length - 2].active">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
            </span>
            <Link :href="films.links[films.links.length - 1].url" class="pagination inline-flex items-center text-sm font-medium rounded-r-md leading-5 hover:text-orange-800 hover:bg-orange-200 focus:z-10 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-500 transition ease-in-out duration-150" v-else>
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
            </Link>
        </div>
    </nav>
</template>
