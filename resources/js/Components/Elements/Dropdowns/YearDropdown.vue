<script setup>
import { ref } from 'vue';

const props = defineProps({
    releaseYear: String,
    setNewReleaseYear: Function
});

// Нужно ли показывать выпадающий список. При монтировании компоненты список скрыт
const isShowList = ref(false);

const yearsList = [];
for(let i = 1901; i <= 2100; i++) {
    yearsList.push(i);
}

// Показывает/Скрывает выпадающий список
const toggleShowList = async function() {
    isShowList.value = !isShowList.value;
};

const handlerSelect = function(e) {
    if(e.target.tagName.toLowerCase() === 'li') {
        toggleShowList();
        props.setNewReleaseYear(e.target.innerHTML === 'все' ? '' : e.target.innerHTML);
    }
};
</script>

<template>
    <button
        class="dropdown-filter-button"
        @click="toggleShowList"
    >
        {{ !!props.releaseYear ? props.releaseYear : 'все' }}
    </button>
    <div class="relative mt-0.5">
        <ul
            class="absolute w-full h-96 overflow-scroll bg-white text-gray-500 font-medium cursor-pointer"
            @click="handlerSelect"
            v-if="isShowList"
        >
            <li
                class="text-center py-1 border-b"
                :class="releaseYear == '' ? 'bg-orange-200 cursor-not-allowed' : 'hover:bg-orange-50 hover:text-gray-700'"
            >все</li>
            <li
                class="text-center py-1 border-b"
                :class="year == releaseYear ? 'bg-orange-200 cursor-not-allowed' : 'hover:bg-orange-50 hover:text-gray-700'"
                v-for="year in yearsList"
            >
                {{ year }}
            </li>
        </ul>
    </div>
</template>
