import { onMounted, onUpdated, ref } from 'vue';

export function useAutofocus(autofocus) {
    const item = autofocus ? ref(null) : null;

    onMounted(() => {
        if(autofocus && item.value) {
            item.value.focus();
        }
    });

    onUpdated(() => {
        if(autofocus && item.value) {
            item.value.focus();
        }
    });
    
    return item;
}
