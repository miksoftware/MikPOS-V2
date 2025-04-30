<?php

namespace App\Filament\Resources\EspacioResource\Pages;

use App\Filament\Resources\EspacioResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEspacio extends EditRecord
{
    protected static string $resource = EspacioResource::class;

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

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $usuarioActual = auth()->user();
        $esSuperAdmin = $usuarioActual->is_super_admin || $usuarioActual->hasRole('Super Admin');

        if (!$esSuperAdmin && $usuarioActual->sucursal_id) {
            $data['sucursal_id'] = $usuarioActual->sucursal_id;
        }

        return $data;
    }
}
