<?php

namespace App\Filament\Resources\EncargadoResource\Pages;

use App\Filament\Resources\EncargadoResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateEncargado extends CreateRecord
{
    protected static string $resource = EncargadoResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
