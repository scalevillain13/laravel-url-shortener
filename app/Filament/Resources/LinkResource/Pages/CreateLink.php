<?php

namespace App\Filament\Resources\LinkResource\Pages;

use App\Actions\CreateShortLinkAction;
use App\Filament\Resources\LinkResource;
use App\Http\Requests\StoreLinkRequest;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CreateLink extends CreateRecord
{
    protected static string $resource = LinkResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $validated = validator($data, (new StoreLinkRequest)->rules())->validate();

        return app(CreateShortLinkAction::class)->execute(Auth::user(), $validated);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
