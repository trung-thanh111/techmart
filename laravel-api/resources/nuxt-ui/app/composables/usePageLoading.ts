export function usePageLoading() {
    const progress = ref(0);
    const isLoading = ref(false);
    let timer: NodeJS.Timeout | null = null;

    const start = () => {
        isLoading.value = true;
        progress.value = 0;

        timer = setInterval(() => {
            if (progress.value < 90) {
                progress.value += Math.random() * 10;
            }
        }, 200);
    };

    const finish = () => {
        if (timer) clearInterval(timer);
        progress.value = 100;
        setTimeout(() => {
            isLoading.value = false;
            progress.value = 0;
        }, 300);
    };

    return { progress, isLoading, start, finish };
}
