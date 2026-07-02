<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\LinkResource;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class DashboardWelcomeWidget extends Widget
{
    protected static string $view = 'filament.widgets.dashboard-welcome';

    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    public function getUserName(): string
    {
        return Auth::user()?->name ?? 'Пользователь';
    }

    public function getCreateLinkUrl(): string
    {
        return LinkResource::getUrl('create');
    }

    public function getLinksUrl(): string
    {
        return LinkResource::getUrl('index');
    }
}
