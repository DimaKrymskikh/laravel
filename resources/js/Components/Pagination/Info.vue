<script setup>

const { films } = defineProps({
    films: Object
});

</script>

<template>
    Показано {{ films.data.length }} фильмов с {{ films.from }} по {{ films.to }} из {{ films.total }}
</template>

