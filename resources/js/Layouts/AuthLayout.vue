<script setup>
import { inject, ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import HouseSvg from '@/Components/Svg/HouseSvg.vue';
import ForbiddenModal from '@/components/Modal/ForbiddenModal.vue';

const { user } = defineProps({
    user: Object | null,
    errors: Object | null
});

const message = ref('');

Echo.private(`film.${user.id}`)
    .listen('AddFilm', (e) => {
        message.value = e.message;
    })
    .listen('RemoveFilm', (e) => {
        message.value = e.message;
    });

const filmsCatalog = inject('filmsCatalog');
const filmsAccount = inject('filmsAccount');
</script>

<template>
    <nav id="main-nav" class="bg-orange-300 shadow shadow-orange-200">
        <div class="lg:container flex justify-between">
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
                <li class="nav-tab" v-if="user.is_admin">
                    <Link class="nav-link small-caps" href="/admin">
                        администрирование
                    </Link>
                </li>
                <li class="nav-tab">
                    <Link class="nav-link small-caps" href="/logout">выход</Link>
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
