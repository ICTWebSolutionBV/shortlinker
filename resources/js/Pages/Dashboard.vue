<script setup>
import { computed } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { formatDate } from '@/utils/datetime.js'
import { Line } from 'vue-chartjs'
import {
    Chart as ChartJS, CategoryScale, LinearScale, PointElement,
    LineElement, Title, Tooltip, Legend, Filler,
} from 'chart.js'

ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, Title, Tooltip, Legend, Filler)

const props = defineProps({
    stats: Object,
    daily: Array,
    topLinks: Array,
    recentLinks: Array,
    appUrl: { type: String, default: '' },
})

const fmt = (n) => (typeof n === 'number' ? n.toLocaleString('en-US') : '0')
const truncate = (str, len = 40) => str && str.length > len ? str.slice(0, len) + '…' : str
const shortUrl = (alias) => `${props.appUrl}/${alias}`

const chartData = computed(() => ({
    labels: props.daily.map(d => d.day.slice(5)),
    datasets: [{
        label: 'Clicks',
        data: props.daily.map(d => d.count),
        borderColor: 'rgb(16, 185, 129)',
        backgroundColor: 'rgba(16, 185, 129, 0.12)',
        fill: true, tension: 0.3, pointRadius: 2, pointHoverRadius: 4, borderWidth: 2,
    }],
}))

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: { legend: { display: false } },
    scales: {
        y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: 'rgba(148,163,184,0.15)' } },
        x: { grid: { display: false } },
    },
}
</script>

<template>
    <AppLayout>
        <Head title="Dashboard" />
        <div class="max-w-6xl mx-auto space-y-6">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Dashboard</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Overview of your short links</p>
                </div>
                <Link :href="route('links.create')"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-xl transition-colors text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    New Short Link
                </Link>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-5">
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ fmt(stats.total_links) }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Total links</p>
                </div>
                <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-5">
                    <p class="text-3xl font-bold text-emerald-600 dark:text-emerald-400">{{ fmt(stats.active_links) }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Active links</p>
                </div>
                <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-5">
                    <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ fmt(stats.clicks_today) }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Clicks today</p>
                </div>
                <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-5">
                    <p class="text-3xl font-bold text-purple-600 dark:text-purple-400">{{ fmt(stats.clicks_month) }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Clicks (30 days)</p>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-5">
                <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Click trend — last 30 days</h2>
                <div class="h-52"><Line :data="chartData" :options="chartOptions" /></div>
            </div>

            <div class="grid lg:grid-cols-2 gap-6">
                <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-800 flex items-center justify-between">
                        <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Top links by clicks</h2>
                        <Link :href="route('links.index', { sort: 'clicks' })" class="text-xs text-primary-600 dark:text-primary-400 hover:underline">View all</Link>
                    </div>
                    <div v-if="topLinks.length === 0" class="py-10 text-center text-sm text-gray-400">No links yet</div>
                    <div v-else class="divide-y divide-gray-100 dark:divide-gray-800">
                        <div v-for="link in topLinks" :key="link.id"
                            class="flex items-center gap-4 px-5 py-3 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ link.title || `/${link.alias}` }}</p>
                                <a :href="shortUrl(link.alias)" target="_blank" class="text-xs font-mono text-primary-600 dark:text-primary-400 hover:underline">/{{ link.alias }}</a>
                            </div>
                            <div class="text-right shrink-0">
                                <p class="text-sm font-bold text-gray-900 dark:text-white">{{ fmt(link.clicks_count) }}</p>
                                <p class="text-xs text-gray-400">clicks</p>
                            </div>
                            <Link :href="route('links.analytics', link.id)" class="p-1.5 rounded-lg text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            </Link>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-800 flex items-center justify-between">
                        <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Recently created</h2>
                        <Link :href="route('links.index')" class="text-xs text-primary-600 dark:text-primary-400 hover:underline">View all</Link>
                    </div>
                    <div v-if="recentLinks.length === 0" class="py-10 text-center">
                        <p class="text-sm text-gray-400">No links yet</p>
                        <Link :href="route('links.create')" class="mt-3 inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">Create your first link</Link>
                    </div>
                    <div v-else class="divide-y divide-gray-100 dark:divide-gray-800">
                        <div v-for="link in recentLinks" :key="link.id"
                            class="flex items-center gap-4 px-5 py-3 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ link.title || `/${link.alias}` }}</p>
                                <p class="text-xs text-gray-400 truncate">{{ truncate(link.original_url) }}</p>
                            </div>
                            <p class="text-xs text-gray-400 shrink-0">{{ formatDate(link.created_at) }}</p>
                            <Link :href="route('links.edit', link.id)" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
