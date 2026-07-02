<?php

namespace App\Filament\Resources\LinkResource\Pages;

use App\Filament\Resources\LinkResource;
use App\Filament\Resources\LinkResource\Widgets\LinkClicksChart;
use App\Models\Link;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewLink extends ViewRecord
{
    protected static string $resource = LinkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('qrCode')
                ->label('QR-код')
                ->icon('heroicon-o-qr-code')
                ->url(fn (Link $record): string => route('links.qr', $record))
                ->openUrlInNewTab(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            LinkClicksChart::make([
                'record' => $this->getRecord(),
            ]),
        ];
    }
}
