<?php

    namespace App\Filament\Resources\AreaPreparacionResource\Pages;

    use App\Filament\Resources\AreaPreparacionResource;
    use Filament\Actions;
    use Filament\Resources\Pages\EditRecord;

    class EditAreaPreparacion extends EditRecord
    {
        protected static string $resource = AreaPreparacionResource::class;

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
