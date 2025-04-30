<?php

namespace App\Filament\Resources\EncargadoResource\Pages;

use App\Filament\Resources\EncargadoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEncargado extends EditRecord
{
    protected static string $resource = EncargadoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
