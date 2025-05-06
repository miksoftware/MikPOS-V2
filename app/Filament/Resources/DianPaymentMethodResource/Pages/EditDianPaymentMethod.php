<?php

namespace App\Filament\Resources\DianPaymentMethodResource\Pages;

use App\Filament\Resources\DianPaymentMethodResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDianPaymentMethod extends EditRecord
{
    protected static string $resource = DianPaymentMethodResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
