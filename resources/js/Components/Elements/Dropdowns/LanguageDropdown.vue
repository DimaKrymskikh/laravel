<script setup>
import { inject, ref } from 'vue';
import Spinner from '@/components/Svg/Spinner.vue';

const props = defineProps({
    languageName: String,
    setNewLanguageName: Function
});

const app = inject('app');
const languagesList = inject('languagesList');

// Нужно ли показывать выпадающий список. При монтировании компоненты список скрыт
const isShowList = ref(false);

// Показывает/Скрывает выпадающий список
const toggleShowList = async function() {
    isShowList.value = !isShowList.value;
    
    if(isShowList.value && languagesList.languages.length === 0) {
        languagesList.languages = await app.request(`/languages/getJson`, 'GET');
    }
};

const handlerSelect = function(e) {
    if(e.target.tagName.toLowerCase() === 'li') {
        toggleShowList();
        props.setNewLanguageName(e.target.innerHTML.includes('все') ? '' : e.target.innerHTML);
    }
};
</script>

<template>
    <button
        class="dropdown-filter-button"
        @click="toggleShowList"
    >
        {{ !!props.languageName ? props.languageName : 'все' }}
    </button>
    <div class="relative mt-0.5">
        <ul
            class="absolute w-full bg-white text-gray-500 font-medium cursor-pointer"
            @click="handlerSelect"
            v-if="isShowList"
        >
            <li
                class="relative text-center py-1 border-b"
                :class="languageName == '' ? 'bg-orange-200 cursor-not-allowed' : 'hover:bg-orange-50 hover:text-gray-700'"
            >
                все
                <Spinner class="spinner" styleSpinner="h-6 fill-gray-700 text-gray-200" v-if="app.isRequest" />
            </li>
            <li
                class="text-center py-1 border-b"
                :class="language.name == languageName ? 'bg-orange-200 cursor-not-allowed' : 'hover:bg-orange-50 hover:text-gray-700'"
                v-for="language in languagesList.languages"
            >
                {{ language.name }}
            </li>
        </ul>
    </div>
</template>
