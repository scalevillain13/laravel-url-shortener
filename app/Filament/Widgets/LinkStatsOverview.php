<?php

namespace App\Filament\Widgets;

use App\Models\Click;
use App\Models\Link;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class LinkStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $userId = Auth::id();

        $linksCount = Link::where('user_id', $userId)->count();

        $clicksCount = Click::whereHas('link', fn ($query) => $query->where('user_id', $userId))->count();

        $clicksToday = Click::whereHas('link', fn ($query) => $query->where('user_id', $userId))
            ->whereDate('clicked_at', today())
            ->count();

        return [
            Stat::make('Мои ссылки', $linksCount)
                ->description('Всего создано коротких ссылок')
                ->icon('heroicon-o-link'),
            Stat::make('Всего переходов', $clicksCount)
                ->description('По всем ссылкам')
                ->icon('heroicon-o-cursor-arrow-rays'),
            Stat::make('Переходов сегодня', $clicksToday)
                ->description(today()->format('d.m.Y'))
                ->icon('heroicon-o-calendar'),
        ];
    }
}
