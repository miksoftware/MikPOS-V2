<?php

namespace App\Filament\Resources\IngredienteResource\Pages;

use App\Filament\Resources\IngredienteResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListIngredientes extends ListRecords
{
    protected static string $resource = IngredienteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
