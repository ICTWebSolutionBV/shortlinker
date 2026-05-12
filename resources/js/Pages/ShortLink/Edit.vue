<script setup>
import { ref, watch, computed } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    link: Object,
    appUrl: { type: String, default: '' },
})

const expiryOptions = [
    { label: 'Never', value: 'never' },
    { label: '1 Hour', value: '1h' },
    { label: '2 Hours', value: '2h' },
    { label: '4 Hours', value: '4h' },
    { label: '6 Hours', value: '6h' },
    { label: '12 Hours', value: '12h' },
    { label: '1 Day', value: '1d' },
    { label: '2 Days', value: '2d' },
    { label: '3 Days', value: '3d' },
    { label: '5 Days', value: '5d' },
    { label: '7 Days', value: '7d' },
    { label: '14 Days', value: '14d' },
    { label: '30 Days', value: '30d' },
    { label: 'Custom…', value: 'custom' },
]

const form = useForm({
    original_url: props.link.original_url,
    alias: props.link.alias,
    title: props.link.title || '',
    is_active: props.link.is_active,
    is_burn: props.link.is_burn ?? false,
    is_tracking: props.link.is_tracking ?? true,
    expires_in: props.link.expires_at ? 'custom' : 'never',
    expires_at: props.link.expires_at
        ? new Date(props.link.expires_at).toISOString().slice(0, 16)
        : '',
})

watch(() => form.expires_in, (v) => { if (v !== 'custom') form.expires_at = '' })

const showQr = ref(false)

function submit() {
    form.put(route('links.update', props.link.id))
}
</script>

<template>
    <AppLayout>
        <Head title="Edit Short Link" />

        <div class="max-w-2xl mx-auto">
            <!-- Header -->
            <div class="flex items-center gap-3 mb-8">
                <Link :href="route('links.index')"
                    class="p-2 rounded-xl text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </Link>
                <div>
                    <h1 class="text-xl font-bold text-gray-900 dark:text-white">Edit Short Link</h1>
                    <p class="text-sm font-mono text-primary-600 dark:text-primary-400 mt-0.5">{{ appUrl }}/{{ link.alias }}</p>
                </div>
            </div>

            <form @submit.prevent="submit">
                <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">

                    <!-- Destination -->
                    <div class="p-6 space-y-3">
                        <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Destination URL</label>
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-3.5 flex items-center">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                            </div>
                            <input v-model="form.original_url" type="url" required
                                :class="form.errors.original_url ? 'border-red-400 focus:ring-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-primary-500'"
                                class="w-full pl-10 pr-4 py-3 text-sm border rounded-xl bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:bg-white dark:focus:bg-gray-800 transition-colors" />
                            <p v-if="form.errors.original_url" class="mt-1.5 text-xs text-red-500">{{ form.errors.original_url }}</p>
                        </div>
                        <input v-model="form.title" type="text" placeholder="Title (optional)"
                            class="w-full px-4 py-3 text-sm border border-gray-200 dark:border-gray-700 rounded-xl bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white dark:focus:bg-gray-800 transition-colors" />
                    </div>

                    <div class="h-px bg-gray-100 dark:bg-gray-800"></div>

                    <!-- Alias -->
                    <div class="p-6 space-y-3">
                        <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Short Link</label>
                        <div class="flex items-center gap-0 bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                            <span class="px-3.5 py-3 text-gray-400 text-xs border-r border-gray-200 dark:border-gray-700 whitespace-nowrap shrink-0 font-mono">{{ appUrl }}/</span>
                            <span class="px-3.5 py-3 text-primary-600 dark:text-primary-400 font-mono font-semibold text-sm tracking-wide">{{ form.alias || 'alias' }}</span>
                        </div>
                        <input v-model="form.alias" type="text" required minlength="5"
                            pattern="[a-zA-Z0-9_-]+"
                            :class="form.errors.alias ? 'border-red-400 focus:ring-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-primary-500'"
                            class="w-full px-4 py-3 text-sm border rounded-xl bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:bg-white dark:focus:bg-gray-800 font-mono transition-colors" />
                        <p class="text-xs text-gray-400">Letters, numbers, hyphens and underscores. Min. 5 characters.</p>
                        <p v-if="form.errors.alias" class="text-xs text-red-500">{{ form.errors.alias }}</p>
                    </div>

                    <div class="h-px bg-gray-100 dark:bg-gray-800"></div>

                    <!-- Options -->
                    <div class="p-6 space-y-4">
                        <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Options</label>

                        <div class="grid grid-cols-2 gap-4">
                            <!-- Active toggle -->
                            <label class="flex items-start gap-3 cursor-pointer select-none group p-3 rounded-xl bg-gray-50 dark:bg-gray-800/60 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                                <div class="relative shrink-0 mt-0.5">
                                    <input type="checkbox" v-model="form.is_active" class="sr-only" />
                                    <div :class="form.is_active ? 'bg-primary-600' : 'bg-gray-200 dark:bg-gray-700'" class="w-9 h-5 rounded-full transition-colors"></div>
                                    <div :class="form.is_active ? 'translate-x-4' : 'translate-x-0'" class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform"></div>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Link is active</span>
                                    <p class="text-xs text-gray-400 mt-0.5">Inactive links return a 404</p>
                                </div>
                            </label>

                            <!-- Burn toggle -->
                            <label class="flex items-start gap-3 cursor-pointer select-none group p-3 rounded-xl bg-gray-50 dark:bg-gray-800/60 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                                :class="form.is_burn ? 'bg-orange-50 dark:bg-orange-900/10 hover:bg-orange-50 dark:hover:bg-orange-900/10' : ''">
                                <div class="relative shrink-0 mt-0.5">
                                    <input type="checkbox" v-model="form.is_burn" class="sr-only" />
                                    <div :class="form.is_burn ? 'bg-orange-500' : 'bg-gray-200 dark:bg-gray-700'" class="w-9 h-5 rounded-full transition-colors"></div>
                                    <div :class="form.is_burn ? 'translate-x-4' : 'translate-x-0'" class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform"></div>
                                </div>
                                <div>
                                    <span class="text-sm font-medium flex items-center gap-1.5" :class="form.is_burn ? 'text-orange-600 dark:text-orange-400' : 'text-gray-700 dark:text-gray-300'">
                                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C9.5 6 7 8.5 7 12a5 5 0 0010 0c0-2.5-1.5-5-5-10z"/></svg>
                                        Burn after read
                                    </span>
                                    <p class="text-xs text-gray-400 mt-0.5">Deletes after first click</p>
                                </div>
                            </label>

                            <!-- Tracking toggle -->
                            <label class="flex items-start gap-3 cursor-pointer select-none group p-3 rounded-xl bg-gray-50 dark:bg-gray-800/60 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                                <div class="relative shrink-0 mt-0.5">
                                    <input type="checkbox" v-model="form.is_tracking" class="sr-only" />
                                    <div :class="form.is_tracking ? 'bg-primary-600' : 'bg-gray-200 dark:bg-gray-700'" class="w-9 h-5 rounded-full transition-colors"></div>
                                    <div :class="form.is_tracking ? 'translate-x-4' : 'translate-x-0'" class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform"></div>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Track clicks</span>
                                    <p class="text-xs text-gray-400 mt-0.5">When off, clicks aren't logged</p>
                                </div>
                            </label>

                            <!-- Expiry -->
                            <div class="p-3 rounded-xl bg-gray-50 dark:bg-gray-800/60 space-y-2">
                                <div class="flex items-center gap-2">
                                    <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Expires</span>
                                    <span v-if="form.expires_in === '14d'" class="text-xs font-medium text-primary-600 dark:text-primary-400">default</span>
                                </div>
                                <select v-model="form.expires_in"
                                    class="w-full text-sm text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg px-2.5 py-1.5 focus:outline-none focus:ring-2 focus:ring-primary-500 cursor-pointer">
                                    <option v-for="opt in expiryOptions" :key="opt.value" :value="opt.value">
                                        {{ opt.label }}{{ opt.value === '14d' ? ' (default)' : '' }}
                                    </option>
                                </select>
                                <p class="text-xs text-gray-400">
                                    Default: <strong class="text-gray-600 dark:text-gray-300 font-semibold">14 days</strong>
                                </p>
                            </div>
                        </div>

                        <!-- Custom date picker -->
                        <Transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0 -translate-y-1" enter-to-class="opacity-100 translate-y-0">
                            <div v-if="form.expires_in === 'custom'">
                                <input v-model="form.expires_at" type="datetime-local"
                                    :class="form.errors.expires_at ? 'border-red-400 focus:ring-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-primary-500'"
                                    class="w-full px-4 py-3 text-sm border rounded-xl bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:bg-white dark:focus:bg-gray-800 transition-colors" />
                                <p v-if="form.errors.expires_at" class="mt-1.5 text-xs text-red-500">{{ form.errors.expires_at }}</p>
                            </div>
                        </Transition>
                    </div>

                    <div class="h-px bg-gray-100 dark:bg-gray-800"></div>

                    <!-- QR Code (optional) -->
                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between">
                            <label class="flex items-center gap-3 cursor-pointer select-none group">
                                <div class="relative shrink-0">
                                    <input type="checkbox" v-model="showQr" class="sr-only" />
                                    <div :class="showQr ? 'bg-primary-600' : 'bg-gray-200 dark:bg-gray-700'" class="w-9 h-5 rounded-full transition-colors"></div>
                                    <div :class="showQr ? 'translate-x-4' : 'translate-x-0'" class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform"></div>
                                </div>
                                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer">QR Code</span>
                            </label>
                            <a v-if="showQr" :href="route('links.qr', link.id)"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-lg transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                Download PNG
                            </a>
                        </div>

                        <Transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0 -translate-y-1" enter-to-class="opacity-100 translate-y-0">
                            <div v-if="showQr" class="flex flex-col items-center justify-center py-4 rounded-xl bg-gray-50 dark:bg-gray-800 gap-2">
                                <svg class="w-5 h-5 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                <span class="text-xs text-gray-400">Use "Download PNG" to get the high-res QR code</span>
                            </div>
                        </Transition>
                    </div>

                    <!-- Actions -->
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between">
                        <Link :href="route('links.analytics', link.id)"
                            class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            Analytics
                        </Link>
                        <div class="flex items-center gap-3">
                            <Link :href="route('links.index')"
                                class="px-4 py-2.5 text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 transition-colors">
                                Cancel
                            </Link>
                            <button type="submit" :disabled="form.processing"
                                class="px-5 py-2.5 text-sm font-semibold text-white bg-primary-600 hover:bg-primary-700 rounded-xl transition-colors disabled:opacity-50 shadow-sm">
                                {{ form.processing ? 'Saving…' : 'Save Changes' }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
