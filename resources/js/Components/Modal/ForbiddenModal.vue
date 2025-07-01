<script setup>
import { app } from '@/Services/app';

const hideModal = function() {
    app.isShowForbiddenModal = false;
    app.errorMessage = '';
};
</script>

<template>
    <div
        id="forbidden-modal"
        class="fixed top-0 left-0 right-0 w-full overflow-x-hidden overflow-y-auto h-screen"
        tabindex="-1"
        v-if="app.isShowForbiddenModal"
    >
        <div class="fixed opacity-25 bg-gray-500 w-full h-screen z-5 stop-event"></div>
        <div class="relative top-12 m-auto max-w-xl z-10">
            <div class="relative bg-white rounded-lg shadow">
                <!-- Modal body -->
                <div id="error-message" class="px-6 py-3">
                    {{ app.errorMessage }}
                </div>
                <!-- Modal footer -->
                <div class="flex items-center justify-end px-6 py-3 space-x-2 border-t border-gray-200 rounded-b">
                    <button 
                        type="button" 
                        class="px-3 py-1.5 text-sm text-center font-medium rounded-lg border text-red-700 bg-red-100 hover:bg-red-700 hover:text-red-100 border-red-700"
                        @click="hideModal"
                    >
                        Закрыть
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
