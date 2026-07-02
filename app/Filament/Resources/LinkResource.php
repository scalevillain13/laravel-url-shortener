<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LinkResource\Pages;
use App\Filament\Resources\LinkResource\RelationManagers\ClicksRelationManager;
use App\Models\Link;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class LinkResource extends Resource
{
    protected static ?string $model = Link::class;

    protected static ?string $navigationIcon = 'heroicon-o-link';

    protected static ?string $modelLabel = 'ссылка';

    protected static ?string $pluralModelLabel = 'Мои ссылки';

    protected static ?string $navigationLabel = 'Мои ссылки';

    /**
     * Каждый пользователь видит и управляет только своими ссылками.
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', Auth::id())
            ->withCount('clicks');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('original_url')
                    ->label('Оригинальный URL')
                    ->placeholder('https://example.com/page')
                    ->url()
                    ->required()
                    ->maxLength(2048)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('code')
                    ->label('Код короткой ссылки (необязательно)')
                    ->helperText('Оставьте пустым — код сгенерируется автоматически.')
                    ->alphaNum()
                    ->minLength(3)
                    ->maxLength(16)
                    ->unique(ignoreRecord: true)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('short_url')
                    ->label('Короткая ссылка')
                    ->state(fn (Link $record): string => $record->short_url)
                    ->badge()
                    ->color('primary')
                    ->copyable()
                    ->copyMessage('Скопировано!')
                    ->url(fn (Link $record): string => $record->short_url, shouldOpenInNewTab: true),
                Tables\Columns\TextColumn::make('original_url')
                    ->label('Оригинальный URL')
                    ->limit(50)
                    ->tooltip(fn (Link $record): string => $record->original_url)
                    ->searchable(),
                Tables\Columns\TextColumn::make('clicks_count')
                    ->label('Переходы')
                    ->badge()
                    ->color('success')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Создана')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make()->label('Статистика'),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Ссылок пока нет')
            ->emptyStateDescription('Создайте первую короткую ссылку.');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Информация о ссылке')
                    ->schema([
                        Infolists\Components\TextEntry::make('short_url')
                            ->label('Короткая ссылка')
                            ->state(fn (Link $record): string => $record->short_url)
                            ->badge()
                            ->color('primary')
                            ->copyable()
                            ->url(fn (Link $record): string => $record->short_url, shouldOpenInNewTab: true),
                        Infolists\Components\TextEntry::make('original_url')
                            ->label('Оригинальный URL')
                            ->url(fn (Link $record): string => $record->original_url, shouldOpenInNewTab: true),
                        Infolists\Components\TextEntry::make('clicks_count')
                            ->label('Всего переходов')
                            ->state(fn (Link $record): int => $record->clicks()->count())
                            ->badge()
                            ->color('success'),
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Создана')
                            ->dateTime('d.m.Y H:i'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ClicksRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLinks::route('/'),
            'create' => Pages\CreateLink::route('/create'),
            'view' => Pages\ViewLink::route('/{record}'),
        ];
    }
}
