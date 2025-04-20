import { onMounted, onUnmounted, ref } from 'vue';

/**
 * Содержит логику влияния событий 'inertia:start' и 'inertia:finish' на приложение
 * 
 * @returns {useGlobalRequest.isGlobalRequest}
 */
export function useGlobalRequest() {
    // Запускать или нет глобальный спиннер
    const isGlobalRequest = ref(false);

    const handlerStart = function() {
        isGlobalRequest.value = true;
    };
    const handlerFinish = function() {
        isGlobalRequest.value = false;
    };

    onMounted(() => {
        document.addEventListener('inertia:start', handlerStart);
        document.addEventListener('inertia:finish', handlerFinish);
    });

    onUnmounted(() => {
        document.removeEventListener('inertia:start', handlerStart);
        document.removeEventListener('inertia:finish', handlerFinish);
    });
    
    return isGlobalRequest;
}
