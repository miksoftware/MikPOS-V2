<?php

namespace App\Filament\Resources\AreaPreparacionResource\Pages;

use App\Filament\Resources\AreaPreparacionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAreaPreparacion extends CreateRecord
{
    protected static string $resource = AreaPreparacionResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
