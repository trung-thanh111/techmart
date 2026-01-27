<script setup lang="ts">
const { progress, isLoading } = usePageLoading();
</script>

<template>
    <div>
        <div v-if="isLoading" class="progress fixed-top" style="height: 3px; z-index: 2000">
            <div
                class="progress-bar progress-bar-striped progress-bar-animated"
                role="progressbar"
                :style="{
                    width: progress + '%',
                    background: 'linear-gradient(90deg, #6610f2, #6f42c1, #d63384)',
                }"
            />
        </div>
        <div v-if="isLoading" class="position-fixed top-0 start-0 w-100 h-100" style="z-index: 1999; background: rgba(0, 0, 0, 0.2); backdrop-filter: blur(3px); cursor: not-allowed" />
    </div>
</template>
