<?php

namespace App\Filament\Resources\DianPaymentMethodResource\Pages;

use App\Filament\Resources\DianPaymentMethodResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDianPaymentMethods extends ListRecords
{
    protected static string $resource = DianPaymentMethodResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
