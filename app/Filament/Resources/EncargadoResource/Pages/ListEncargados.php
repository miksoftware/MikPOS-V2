<?php

namespace App\Filament\Resources\EncargadoResource\Pages;

use App\Filament\Resources\EncargadoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEncargados extends ListRecords
{
    protected static string $resource = EncargadoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
