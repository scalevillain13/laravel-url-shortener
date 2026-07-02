<?php

namespace App\Filament\Resources\LinkResource\RelationManagers;

use App\Actions\ExportClicksToCsvAction;
use App\Models\Link;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ClicksRelationManager extends RelationManager
{
    protected static string $relationship = 'clicks';

    protected static ?string $title = 'Переходы';

    protected static ?string $modelLabel = 'переход';

    protected static ?string $pluralModelLabel = 'переходы';

    public function isReadOnly(): bool
    {
        return true;
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP-адрес')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user_agent')
                    ->label('User-Agent')
                    ->limit(60)
                    ->tooltip(fn ($record): ?string => $record->user_agent)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('clicked_at')
                    ->label('Дата и время перехода')
                    ->dateTime('d.m.Y H:i:s')
                    ->sortable(),
            ])
            ->headerActions([
                Tables\Actions\Action::make('exportCsv')
                    ->label('Экспорт CSV')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function (): mixed {
                        /** @var Link $link */
                        $link = $this->getOwnerRecord();

                        return app(ExportClicksToCsvAction::class)->execute($link);
                    }),
            ])
            ->defaultSort('clicked_at', 'desc')
            ->emptyStateHeading('Переходов пока не было');
    }
}
