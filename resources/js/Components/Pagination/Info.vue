<script setup>

const { films } = defineProps({
    films: Object
});

</script>

<template>
    Показано {{ films.per_page }} фильмов с {{ films.from }} по {{ films.to }} из {{ films.total }}
</template>

