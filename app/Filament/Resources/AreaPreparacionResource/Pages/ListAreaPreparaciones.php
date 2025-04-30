<?php

        namespace App\Filament\Resources\AreaPreparacionResource\Pages;

        use App\Filament\Resources\AreaPreparacionResource;
        use Filament\Actions;
        use Filament\Resources\Pages\ListRecords;

        class ListAreaPreparaciones extends ListRecords
        {
            protected static string $resource = AreaPreparacionResource::class;

            protected function getHeaderActions(): array
            {
                return [
                    Actions\CreateAction::make(),
                ];
            }
        }
