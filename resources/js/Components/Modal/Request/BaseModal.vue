<script setup>
import { inject } from 'vue';

const { hideModal } = defineProps({
    headerTitle: String,
    hideModal: Function,
    handlerSubmit: Function | undefined
});

const app = inject('app');

const hideBaseModal = function(e) {
    if(e.currentTarget.classList.contains('disabled') || e.currentTarget.classList.contains('stop-event')) {
        return;
    }
    hideModal();
};
</script>

<template>
    <div
        class="fixed top-0 left-0 right-0 w-full overflow-x-hidden overflow-y-auto h-screen"
        tabindex="-1"
    >
        <div
            id="modal-background"
            class="fixed opacity-25 bg-gray-500 w-full h-screen z-5"
            :class="app.isRequest ? 'stop-event' : ''"
            @click="hideBaseModal"
        ></div>
        <div class="relative top-12 m-auto max-w-xl z-10">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow">
                <!-- Modal header -->
                <div class="flex items-start justify-between px-6 py-3 border-b rounded-t">
                    <div class="text-xl font-semibold text-orange-700">
                        {{ headerTitle }}
                    </div>
                    <button
                        id="modal-cross"
                        type="button" 
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center"
                        :class="app.isRequest ? 'stop-event' : ''"
                        @click="hideBaseModal"
                    >
                        <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg> 
                    </button>
                </div>
                <!-- Modal body -->
                <div class="px-6 py-3">
                    <slot name="body"></slot>
                </div>
                <!-- Modal footer -->
                <div class="flex justify-end px-6 py-3 space-x-2 border-t border-gray-200 rounded-b" v-if="handlerSubmit">
                    <button
                        id="modal-no"
                        type="button"
                        class="px-3 py-1.5 text-sm text-center font-medium rounded-lg border"
                        :class="app.isRequest ? 'disabled' : 'text-green-700 bg-green-100 hover:bg-green-700 hover:text-green-100 border-green-700'"
                        @click="hideBaseModal"
                    >
                        Нет
                    </button>
                    <button 
                        id="modal-yes"
                        type="button" 
                        class="px-3 py-1.5 text-sm text-center font-medium rounded-lg border"
                        :class="app.isRequest ? 'disabled' : 'text-red-700 bg-red-100 hover:bg-red-700 hover:text-red-100 border-red-700'"
                        @click="handlerSubmit"
                    >
                        Да
                    </button>
                </div>
                <div class="flex justify-center px-6 py-3 space-x-2 border-t border-gray-200 rounded-b" v-else>
                    <button
                        id="modal-no"
                        type="button"
                        class="px-3 py-1.5 text-sm text-center font-medium rounded-lg border"
                        :class="app.isRequest ? 'disabled' : ' text-orange-700 bg-orange-100 hover:bg-orange-700 hover:text-orange-100 border-orange-700'"
                        @click="hideBaseModal"
                    >
                        Закрыть
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
