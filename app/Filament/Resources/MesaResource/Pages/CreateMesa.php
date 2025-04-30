<?php

namespace App\Filament\Resources\MesaResource\Pages;

use App\Filament\Resources\MesaResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateMesa extends CreateRecord
{
    protected static string $resource = MesaResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
