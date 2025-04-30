<?php

namespace App\Filament\Resources\SucursalResource\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class SucursalInfo extends Widget
{
    protected static string $view = 'filament.widgets.sucursal-info';

    protected int | string | array $columnSpan = 'full';

    public function getSucursalName(): string
    {
        $user = Auth::user();
        
        if (!$user->sucursal_id) {
            return 'No asignado a ninguna sucursal';
        }

        return $user->sucursal->nombre ?? 'Sucursal no encontrada';
    }

    public function getEmpresaName(): string
    {
        $user = Auth::user();
        
        if (!$user->sucursal_id || !$user->sucursal->empresa_id) {
            return 'No asociado a ninguna empresa';
        }

        return $user->sucursal->empresa->razon_social ?? 'Empresa no encontrada';
    }
}
