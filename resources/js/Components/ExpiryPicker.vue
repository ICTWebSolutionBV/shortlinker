<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import { VueDatePicker } from '@vuepic/vue-datepicker'
import '@vuepic/vue-datepicker/dist/main.css'

// Wall-clock value (`yyyy-MM-ddTHH:mm`); the server reads it in the user's timezone.
const model = defineModel({ type: String, default: '' })

defineProps({
    invalid: { type: Boolean, default: false },
})

const picker = ref(null)
const isDark = ref(false)
let observer

onMounted(() => {
    const root = document.documentElement
    isDark.value = root.classList.contains('dark')
    observer = new MutationObserver(() => { isDark.value = root.classList.contains('dark') })
    observer.observe(root, { attributes: true, attributeFilter: ['class'] })
})

onBeforeUnmount(() => observer?.disconnect())

defineExpose({ open: () => picker.value?.openMenu() })
</script>

<template>
    <VueDatePicker
        ref="picker"
        v-model="model"
        class="expiry-picker"
        :class="{ 'expiry-picker--invalid': invalid }"
        model-type="yyyy-MM-dd'T'HH:mm"
        :formats="{ input: 'dd-MM-yyyy HH:mm', preview: 'dd-MM-yyyy HH:mm' }"
        :min-date="new Date()"
        :week-start="1"
        :time-config="{ is24: true, startTime: { hours: 23, minutes: 59 } }"
        :input-attrs="{ clearable: false }"
        :action-row="{ selectBtnLabel: 'Apply', showNow: false }"
        :dark="isDark"
        placeholder="Pick a date"
        teleport
    />
</template>

<style>
.dp--theme-light {
    --dp-primary-color: var(--color-primary-600);
    --dp-primary-disabled-color: var(--color-primary-300);
    --dp-highlight-color: color-mix(in srgb, var(--color-primary-600) 12%, transparent);
    --dp-border-color: var(--color-gray-200);
    --dp-menu-border-color: var(--color-gray-200);
    --dp-border-color-hover: var(--color-gray-300);
    --dp-border-color-focus: var(--color-primary-500);
    --dp-hover-color: var(--color-gray-100);
    --dp-text-color: var(--color-gray-700);
}

.dp--theme-dark {
    --dp-background-color: var(--color-gray-700);
    --dp-primary-color: var(--color-primary-500);
    --dp-primary-disabled-color: var(--color-primary-800);
    --dp-highlight-color: color-mix(in srgb, var(--color-primary-500) 20%, transparent);
    --dp-border-color: var(--color-gray-600);
    --dp-menu-border-color: var(--color-gray-600);
    --dp-border-color-hover: var(--color-gray-500);
    --dp-border-color-focus: var(--color-primary-500);
    --dp-hover-color: var(--color-gray-600);
    --dp-text-color: var(--color-gray-200);
}

.dp--theme-light,
.dp--theme-dark {
    --dp-font-family: var(--font-sans);
    --dp-font-size: 0.875rem;
    --dp-border-radius: 0.5rem;
    --dp-cell-border-radius: 0.5rem;
    --dp-input-padding: 6px 12px;
    --dp-input-icon-padding: 32px;
}

.dp--menu {
    box-shadow: 0 10px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
}

.expiry-picker .dp--input-focus {
    box-shadow: 0 0 0 2px var(--color-primary-500);
}

.expiry-picker--invalid .dp--input {
    border-color: var(--color-red-400);
}
</style>
