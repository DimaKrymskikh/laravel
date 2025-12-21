<script setup>
import { ref } from 'vue';
import { statisticsOptions, periodicityIntervals } from '@/Services/Content/Weather/weatherStatistics';

// Нужно ли показывать выпадающий список. При монтировании компоненты список скрыт
const isShowList = ref(false);

// Показывает/Скрывает выпадающий список
const toggleShowList = function() {
    isShowList.value = !isShowList.value;
};

const handlerSelect = function(e) {
    if (e.target.classList.contains('select')) {
        return;
    }
    
    if(e.target.tagName.toLowerCase() === 'li') {
        toggleShowList();
        statisticsOptions.interval = e.target.getAttribute('data-interval');
    }
}
</script>

<template>
    <div class="relative">
        <button
            class="bg-orange-200 text-orange-500 px-6 py-2 hover:bg-orange-300 hover:text-orange-700 rounded-lg"
            @click="toggleShowList"
        >
            <div>Интервал периодичности</div>
            <div class="text-blue-700 hover:text-blue-900">{{ statisticsOptions.interval ? statisticsOptions.getIntervalText(statisticsOptions.interval) : 'не выбран' }}</div>
        </button>
        <ul
            class="absolute w-full bg-white"
            @click="handlerSelect"
            v-if="isShowList"
        >
            <li
                class="text-center border-b"
                :class="interval === statisticsOptions.interval ? 'select bg-orange-100 cursor-not-allowed' : 'cursor-pointer'"
                v-for="interval in periodicityIntervals"
                :data-interval="interval"
            >
                {{ statisticsOptions.getIntervalText(interval) }}
            </li>
        </ul>
    </div>
</template>
