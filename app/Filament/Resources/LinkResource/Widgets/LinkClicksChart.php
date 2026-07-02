<?php

namespace App\Filament\Resources\LinkResource\Widgets;

use App\Models\Link;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class LinkClicksChart extends ChartWidget
{
    protected static ?string $heading = 'Переходы за последние 30 дней';

    protected static ?string $maxHeight = '300px';

    public ?Link $record = null;

    protected function getData(): array
    {
        if ($this->record === null) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }

        $startDate = now()->subDays(29)->startOfDay();

        $countsByDate = $this->record->clicks()
            ->where('clicked_at', '>=', $startDate)
            ->select(DB::raw('DATE(clicked_at) as date'), DB::raw('COUNT(*) as total'))
            ->groupBy('date')
            ->pluck('total', 'date');

        $labels = [];
        $data = [];

        foreach (CarbonPeriod::create($startDate, '1 day', now()) as $date) {
            /** @var Carbon $date */
            $key = $date->toDateString();
            $labels[] = $date->format('d.m');
            $data[] = (int) ($countsByDate[$key] ?? 0);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Переходы',
                    'data' => $data,
                    'borderColor' => '#6366f1',
                    'backgroundColor' => 'rgba(99, 102, 241, 0.15)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
