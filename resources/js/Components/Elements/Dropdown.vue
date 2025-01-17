<script setup>
import { inject, ref } from 'vue';

const props = defineProps({
    buttonName: String,         /* Текст кнопки */
    itemsNumberOnPage: Number,  /* Выбранное число в списке */
    changeNumber: Function      /* Изменяет число элементов на странице */
});

const globalConsts = inject('globalConsts');

// Нужно ли показывать выпадающий список. При монтировании компоненты список скрыт
const isShowList = ref(false);

// Показывает/Скрывает выпадающий список
const toggleShowList = function() {
    isShowList.value = !isShowList.value;
};

// Изменяет число элементов на странице
const handlerSelect = function(e) {
    if (e.target.classList.contains('select')) {
        return;
    }
    
    if(e.target.tagName.toLowerCase() === 'li') {
        toggleShowList();
        props.changeNumber(parseInt(e.target.getAttribute('data-number'), 10));
    }
}
</script>

<template>
    <div class="relative">
        <button
            class="bg-orange-200 text-orange-500 px-6 py-2 hover:bg-orange-300 hover:text-orange-700 rounded-lg"
            @click="toggleShowList"
        >
            {{buttonName}}
        </button>
        <ul
            class="absolute w-full bg-white"
            @click="handlerSelect"
            v-if="isShowList"
        >
            <li
                class="text-center border-b"
                :class="n === itemsNumberOnPage ? 'select bg-orange-100 cursor-not-allowed' : 'cursor-pointer'"
                v-for="n in globalConsts.paginatorPerPageList"
                :data-number="n"
            >
                {{ n }}
            </li>
        </ul>
    </div>
</template>
