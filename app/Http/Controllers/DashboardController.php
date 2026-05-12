<?php

namespace App\Http\Controllers;

use App\Models\ShortLink;
use App\Models\ShortLinkClick;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $now = CarbonImmutable::now();

        $totalLinks  = ShortLink::where('user_id', $user->id)->count();
        $activeLinks = ShortLink::where('user_id', $user->id)->where('is_active', true)->count();

        $clicksToday = ShortLinkClick::whereHas('shortLink', fn ($q) => $q->where('user_id', $user->id))
            ->where('clicked_at', '>=', $now->startOfDay())
            ->count();

        $clicksMonth = ShortLinkClick::whereHas('shortLink', fn ($q) => $q->where('user_id', $user->id))
            ->where('clicked_at', '>=', $now->startOfDay()->subDays(29))
            ->count();

        // Daily click trend for the last 30 days
        $since = $now->startOfDay()->subDays(29);
        $dailyRaw = ShortLinkClick::whereHas('shortLink', fn ($q) => $q->where('user_id', $user->id))
            ->where('clicked_at', '>=', $since)
            ->select(DB::raw('DATE(clicked_at) as day'), DB::raw('COUNT(*) as count'))
            ->groupBy('day')
            ->pluck('count', 'day')
            ->all();

        $daily = [];
        for ($i = 0; $i < 30; $i++) {
            $day = $since->addDays($i)->toDateString();
            $daily[] = ['day' => $day, 'count' => (int) ($dailyRaw[$day] ?? 0)];
        }

        // Top 5 links by clicks
        $topLinks = ShortLink::where('user_id', $user->id)
            ->orderByDesc('clicks_count')
            ->limit(5)
            ->get()
            ->map(fn ($l) => [
                'id'           => $l->id,
                'alias'        => $l->alias,
                'title'        => $l->title,
                'original_url' => $l->original_url,
                'clicks_count' => $l->clicks_count,
                'is_active'    => $l->is_active,
            ]);

        // Recent links
        $recentLinks = ShortLink::where('user_id', $user->id)
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn ($l) => [
                'id'           => $l->id,
                'alias'        => $l->alias,
                'title'        => $l->title,
                'original_url' => $l->original_url,
                'clicks_count' => $l->clicks_count,
                'created_at'   => $l->created_at->toISOString(),
            ]);

        return Inertia::render('Dashboard', [
            'stats' => [
                'total_links'  => $totalLinks,
                'active_links' => $activeLinks,
                'clicks_today' => $clicksToday,
                'clicks_month' => $clicksMonth,
            ],
            'daily'       => $daily,
            'topLinks'    => $topLinks,
            'recentLinks' => $recentLinks,
            'appUrl'      => config('app.url'),
        ]);
    }
}
