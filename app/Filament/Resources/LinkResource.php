<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LinkResource\Pages;
use App\Filament\Resources\LinkResource\RelationManagers\ClicksRelationManager;
use App\Http\Requests\StoreLinkRequest;
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
                Forms\Components\Section::make('Основное')
                    ->schema([
                        Forms\Components\TextInput::make('original_url')
                            ->label('Оригинальный URL (HTTPS)')
                            ->placeholder('https://example.com/page')
                            ->url()
                            ->required()
                            ->maxLength(2048)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('code')
                            ->label('Код (необязательно)')
                            ->helperText('Оставьте пустым — код сгенерируется автоматически.')
                            ->alphaNum()
                            ->minLength(3)
                            ->maxLength(16)
                            ->rules(fn (): array => StoreLinkRequest::baseRules()['code'])
                            ->unique(ignoreRecord: true)
                            ->columnSpanFull(),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Активна')
                            ->default(true),
                        Forms\Components\DateTimePicker::make('expires_at')
                            ->label('Истекает')
                            ->nullable()
                            ->minDate(now()),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('UTM-метки')
                    ->description('Добавляются к URL при переходе по короткой ссылке.')
                    ->schema([
                        Forms\Components\TextInput::make('utm_source')->label('utm_source')->maxLength(100),
                        Forms\Components\TextInput::make('utm_medium')->label('utm_medium')->maxLength(100),
                        Forms\Components\TextInput::make('utm_campaign')->label('utm_campaign')->maxLength(100),
                    ])
                    ->columns(3)
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('short_url')
                    ->label('Короткая')
                    ->state(fn (Link $record): string => $record->short_url)
                    ->copyable()
                    ->url(fn (Link $record): string => $record->short_url, shouldOpenInNewTab: true),
                Tables\Columns\TextColumn::make('original_url')
                    ->label('Оригинал')
                    ->limit(40)
                    ->tooltip(fn (Link $record): string => $record->original_url)
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Активна')
                    ->boolean(),
                Tables\Columns\TextColumn::make('clicks_count')
                    ->label('Переходы')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('expires_at')
                    ->label('Истекает')
                    ->dateTime('d.m.Y H:i')
                    ->placeholder('—')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Создана')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('Активность'),
                Tables\Filters\Filter::make('expired')
                    ->label('Истекшие')
                    ->query(fn (Builder $query): Builder => $query
                        ->whereNotNull('expires_at')
                        ->where('expires_at', '<', now())),
                Tables\Filters\Filter::make('min_clicks')
                    ->form([
                        Forms\Components\TextInput::make('count')
                            ->label('Мин. переходов')
                            ->numeric()
                            ->minValue(0),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (blank($data['count'] ?? null)) {
                            return $query;
                        }

                        return $query->has('clicks', '>=', (int) $data['count']);
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make()->label('Статистика'),
                Tables\Actions\Action::make('qrCode')
                    ->label('QR')
                    ->icon('heroicon-o-qr-code')
                    ->url(fn (Link $record): string => route('links.qr', $record))
                    ->openUrlInNewTab(),
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
                            ->copyable()
                            ->url(fn (Link $record): string => $record->short_url, shouldOpenInNewTab: true),
                        Infolists\Components\TextEntry::make('redirect_url')
                            ->label('URL редиректа (с UTM)')
                            ->url(fn (Link $record): string => $record->redirect_url, shouldOpenInNewTab: true),
                        Infolists\Components\IconEntry::make('is_active')->label('Активна')->boolean(),
                        Infolists\Components\TextEntry::make('expires_at')->label('Истекает')->dateTime('d.m.Y H:i')->placeholder('—'),
                        Infolists\Components\TextEntry::make('clicks_count')
                            ->label('Всего переходов')
                            ->state(fn (Link $record): int => $record->clicks()->count())
                            ->badge(),
                        Infolists\Components\TextEntry::make('created_at')->label('Создана')->dateTime('d.m.Y H:i'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function getRelations(): array
    {
        return [ClicksRelationManager::class];
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
