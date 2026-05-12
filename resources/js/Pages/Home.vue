<script setup>
import { ref, computed, watch } from 'vue'
import { Head, Link, useForm, usePage } from '@inertiajs/vue3'

const props = defineProps({
    appUrl: { type: String, default: '' },
})

const page = usePage()
const user = computed(() => page.props.auth?.user)
const isLoggedIn = computed(() => !!user.value)
const shortened = computed(() => page.props.flash?.shortened || null)

const copied = ref(false)
watch(shortened, (val) => {
    if (val?.short_url) {
        navigator.clipboard.writeText(val.short_url)
        copied.value = true
        setTimeout(() => copied.value = false, 2500)
    }
})

const showOptions = ref(false)
const useCustomAlias = ref(false)

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
]

const form = useForm({
    original_url: '',
    alias: '',
    expires_in: '14d',
    is_burn: false,
})

function submit() {
    form.post(route('shorten'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset()
            useCustomAlias.value = false
            showOptions.value = false
        },
    })
}

function copy(text) {
    navigator.clipboard.writeText(text)
}

function formatExpiry(iso) {
    if (!iso) return null
    const d = new Date(iso)
    return d.toLocaleString(undefined, { dateStyle: 'medium', timeStyle: 'short' })
}
</script>

<template>
    <Head title="Shortlinker — Free URL Shortener" />

    <div class="min-h-screen bg-white dark:bg-gray-950 flex flex-col">
        <!-- Header -->
        <header class="absolute top-0 left-0 right-0 z-30">
            <div class="max-w-5xl mx-auto flex items-center justify-between h-16 px-6">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 bg-primary-600 rounded-lg flex items-center justify-center shrink-0 shadow-sm">
                        <svg class="w-4.5 h-4.5 text-white w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                        </svg>
                    </div>
                    <span class="font-bold text-gray-900 dark:text-white text-lg tracking-tight">Shortlinker</span>
                </div>
                <div class="flex items-center gap-2">
                    <template v-if="isLoggedIn">
                        <Link :href="route('dashboard')"
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-xl transition-colors">
                            Dashboard
                        </Link>
                    </template>
                    <template v-else>
                        <Link :href="route('login')"
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition-colors">
                            Sign in
                        </Link>
                    </template>
                </div>
            </div>
        </header>

        <!-- Hero -->
        <main class="flex-1 flex flex-col">
            <!-- Gradient top section -->
            <div class="relative bg-gradient-to-b from-primary-50 via-white to-white dark:from-gray-900 dark:via-gray-950 dark:to-gray-950 pt-28 pb-20 px-4">
                <!-- Subtle grid pattern -->
                <div class="absolute inset-0 bg-[linear-gradient(to_right,#e0e7ff22_1px,transparent_1px),linear-gradient(to_bottom,#e0e7ff22_1px,transparent_1px)] bg-[size:48px_48px] dark:bg-[linear-gradient(to_right,#ffffff08_1px,transparent_1px),linear-gradient(to_bottom,#ffffff08_1px,transparent_1px)]"></div>

                <div class="relative max-w-2xl mx-auto text-center">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary-50 dark:bg-primary-900/30 border border-primary-100 dark:border-primary-800 text-primary-600 dark:text-primary-400 text-xs font-medium mb-6">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3"/></svg>
                        Free to use · No account required
                    </div>

                    <h1 class="text-5xl sm:text-6xl font-extrabold text-gray-900 dark:text-white leading-[1.1] tracking-tight">
                        Short links,<br/>
                        <span class="text-primary-600 dark:text-primary-400">big impact.</span>
                    </h1>
                    <p class="mt-5 text-lg text-gray-500 dark:text-gray-400 max-w-lg mx-auto leading-relaxed">
                        Paste your URL, get a clean short link in seconds. Add an expiry or make it vanish after one click.
                    </p>
                </div>

                <!-- Shortener Card -->
                <div class="relative max-w-2xl mx-auto mt-10">
                    <form @submit.prevent="submit" class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-xl shadow-gray-100/80 dark:shadow-none p-5 space-y-4">

                        <!-- URL input -->
                        <div class="flex gap-2">
                            <div class="relative flex-1">
                                <div class="pointer-events-none absolute inset-y-0 left-3.5 flex items-center">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                    </svg>
                                </div>
                                <input v-model="form.original_url" type="url" required
                                    placeholder="https://your-long-url.com/paste-here"
                                    :class="form.errors.original_url ? 'border-red-400 focus:ring-red-500' : 'border-gray-200 dark:border-gray-700 focus:ring-primary-500'"
                                    class="w-full pl-10 pr-4 py-3 text-sm border rounded-xl bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:bg-white dark:focus:bg-gray-800 transition-colors" />
                            </div>
                            <button type="submit" :disabled="form.processing"
                                class="shrink-0 px-5 py-3 bg-primary-600 hover:bg-primary-700 active:bg-primary-800 text-white font-semibold rounded-xl transition-colors disabled:opacity-50 text-sm shadow-sm shadow-primary-200 dark:shadow-none">
                                {{ form.processing ? '…' : 'Shorten' }}
                            </button>
                        </div>
                        <p v-if="form.errors.original_url" class="text-xs text-red-500 -mt-2">{{ form.errors.original_url }}</p>

                        <!-- Options row -->
                        <div class="flex flex-wrap items-center gap-3 pt-1">
                            <!-- Expiry dropdown -->
                            <div class="flex items-center gap-2">
                                <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <label class="text-xs font-medium text-gray-500 dark:text-gray-400">Expires</label>
                                <select v-model="form.expires_in"
                                    class="text-xs font-medium text-gray-700 dark:text-gray-200 bg-gray-100 dark:bg-gray-800 border-0 rounded-lg px-2.5 py-1.5 focus:outline-none focus:ring-2 focus:ring-primary-500 cursor-pointer">
                                    <option v-for="opt in expiryOptions" :key="opt.value" :value="opt.value">
                                        {{ opt.label }}{{ opt.value === '14d' ? ' (default)' : '' }}
                                    </option>
                                </select>
                                <span v-if="form.expires_in === '14d'" class="text-xs text-primary-500 dark:text-primary-400 font-medium">default</span>
                            </div>

                            <div class="w-px h-4 bg-gray-200 dark:bg-gray-700"></div>

                            <!-- Burn toggle -->
                            <label class="flex items-center gap-2 cursor-pointer select-none group">
                                <div class="relative">
                                    <input type="checkbox" v-model="form.is_burn" class="sr-only" />
                                    <div :class="form.is_burn ? 'bg-orange-500' : 'bg-gray-200 dark:bg-gray-700'"
                                        class="w-8 h-4 rounded-full transition-colors"></div>
                                    <div :class="form.is_burn ? 'translate-x-4' : 'translate-x-0'"
                                        class="absolute top-0.5 left-0.5 w-3 h-3 bg-white rounded-full shadow transition-transform"></div>
                                </div>
                                <span class="flex items-center gap-1 text-xs font-medium" :class="form.is_burn ? 'text-orange-600 dark:text-orange-400' : 'text-gray-500 dark:text-gray-400'">
                                    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M12 2C9.5 6 7 8.5 7 12a5 5 0 0010 0c0-2.5-1.5-5-5-10z"/>
                                    </svg>
                                    Burn after read
                                </span>
                            </label>

                            <div class="w-px h-4 bg-gray-200 dark:bg-gray-700"></div>

                            <!-- Custom alias toggle -->
                            <button type="button" @click="useCustomAlias = !useCustomAlias"
                                class="flex items-center gap-1.5 text-xs font-medium transition-colors"
                                :class="useCustomAlias ? 'text-primary-600 dark:text-primary-400' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                                </svg>
                                Custom alias
                            </button>
                        </div>

                        <!-- Custom alias input -->
                        <Transition
                            enter-active-class="transition ease-out duration-200"
                            enter-from-class="opacity-0 -translate-y-1"
                            enter-to-class="opacity-100 translate-y-0"
                            leave-active-class="transition ease-in duration-150"
                            leave-from-class="opacity-100 translate-y-0"
                            leave-to-class="opacity-0 -translate-y-1">
                            <div v-if="useCustomAlias">
                                <div class="flex items-center gap-0 bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden text-sm">
                                    <span class="px-3 py-2.5 text-gray-400 text-xs border-r border-gray-200 dark:border-gray-700 whitespace-nowrap shrink-0 font-mono">{{ appUrl }}/</span>
                                    <input v-model="form.alias" type="text" placeholder="my-alias (min. 5 chars)" minlength="5"
                                        pattern="[a-zA-Z0-9_-]+"
                                        class="flex-1 px-3 py-2.5 bg-transparent text-sm text-gray-900 dark:text-white placeholder-gray-400 font-mono focus:outline-none" />
                                </div>
                                <p v-if="form.errors.alias" class="mt-1.5 text-xs text-red-500">{{ form.errors.alias }}</p>
                            </div>
                        </Transition>
                    </form>

                    <!-- Default expiry note -->
                    <p class="mt-2 text-center text-xs text-gray-400 dark:text-gray-500">
                        <svg class="inline w-3 h-3 mr-0.5 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Links expire after <strong class="text-gray-600 dark:text-gray-300 font-semibold">14 days</strong> by default. Change anytime above.
                    </p>

                    <!-- Result -->
                    <Transition
                        enter-active-class="transition ease-out duration-300"
                        enter-from-class="opacity-0 translate-y-2"
                        enter-to-class="opacity-100 translate-y-0">
                        <div v-if="shortened" class="mt-3 bg-white dark:bg-gray-900 rounded-2xl border border-emerald-200 dark:border-emerald-800/60 p-4 shadow-lg shadow-emerald-50 dark:shadow-none">
                            <div class="flex items-center gap-2 mb-3">
                                <div class="w-5 h-5 rounded-full bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center shrink-0">
                                    <svg class="w-3 h-3 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <span class="text-sm font-semibold text-emerald-700 dark:text-emerald-400">Link ready!</span>
                                <div v-if="shortened.is_burn" class="ml-auto flex items-center gap-1 px-2 py-0.5 rounded-full bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800">
                                    <svg class="w-3 h-3 text-orange-500" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C9.5 6 7 8.5 7 12a5 5 0 0010 0c0-2.5-1.5-5-5-10z"/></svg>
                                    <span class="text-xs font-medium text-orange-600 dark:text-orange-400">Burns after 1 view</span>
                                </div>
                                <div v-else-if="shortened.expires_at" class="ml-auto flex items-center gap-1 text-xs text-gray-400">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Expires {{ formatExpiry(shortened.expires_at) }}
                                </div>
                            </div>
                            <div class="flex items-center gap-2 bg-gray-50 dark:bg-gray-800 rounded-xl px-4 py-3">
                                <a :href="shortened.short_url" target="_blank"
                                    class="flex-1 text-primary-600 dark:text-primary-400 font-mono font-semibold text-sm hover:underline truncate">
                                    {{ shortened.short_url }}
                                </a>
                                <button @click="copy(shortened.short_url); copied = true; setTimeout(() => copied = false, 2500)"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg transition-colors shrink-0"
                                    :class="copied ? 'bg-emerald-600 text-white' : 'bg-primary-600 hover:bg-primary-700 text-white'">
                                    <svg v-if="copied" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    <svg v-else class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                    {{ copied ? 'Copied!' : 'Copy' }}
                                </button>
                            </div>
                            <p class="mt-2 text-xs text-gray-400 truncate">→ {{ shortened.original_url }}</p>
                            <div v-if="!isLoggedIn" class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-800">
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    <Link :href="route('login')" class="text-primary-600 dark:text-primary-400 font-medium hover:underline">Sign in</Link>
                                    to save links, track clicks, and manage everything in one place.
                                </p>
                            </div>
                        </div>
                    </Transition>
                </div>
            </div>

            <!-- Stats bar -->
            <div class="border-y border-gray-100 dark:border-gray-800/60 bg-gray-50 dark:bg-gray-900/50 py-6 px-4">
                <div class="max-w-xl mx-auto flex items-center justify-center gap-8 sm:gap-16">
                    <div class="text-center">
                        <div class="text-2xl font-extrabold text-gray-900 dark:text-white">Free</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">No account needed</div>
                    </div>
                    <div class="w-px h-10 bg-gray-200 dark:bg-gray-700"></div>
                    <div class="text-center">
                        <div class="text-2xl font-extrabold text-gray-900 dark:text-white">Instant</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Links in seconds</div>
                    </div>
                    <div class="w-px h-10 bg-gray-200 dark:bg-gray-700"></div>
                    <div class="text-center">
                        <div class="text-2xl font-extrabold text-gray-900 dark:text-white">Smart</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Expiry &amp; burn options</div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="py-5 px-4 text-center">
            <p class="text-xs text-gray-400 dark:text-gray-600">&copy; {{ new Date().getFullYear() }} ICTWebSolution B.V. All rights reserved.</p>
        </footer>
    </div>
</template>
