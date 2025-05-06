<?php

namespace App\Filament\Resources\IngredienteResource\Pages;

use App\Filament\Resources\IngredienteResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditIngrediente extends EditRecord
{
    protected static string $resource = IngredienteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
