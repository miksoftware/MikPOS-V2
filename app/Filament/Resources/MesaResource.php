<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MesaResource\Pages;
use App\Models\Mesa;
use App\Models\Espacio;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MesaResource extends Resource
{
    protected static ?string $model = Mesa::class;

    protected static ?string $navigationIcon = 'heroicon-o-table-cells';

    protected static ?string $navigationGroup = 'Restaurante';

    protected static ?int $navigationSort = 15;

    public static function form(Form $form): Form
    {
        $usuarioActual = auth()->user();
        $esSuperAdmin = $usuarioActual->is_super_admin || $usuarioActual->hasRole('Super Admin');

        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('numero_puestos')
                    ->required()
                    ->numeric()
                    ->default(4)
                    ->minValue(1)
                    ->maxValue(50),
                Forms\Components\Select::make('espacio_id')
                    ->label('Espacio')
                    ->required()
                    ->relationship(
                        name: 'espacio',
                        titleAttribute: 'nombre',
                        modifyQueryUsing: function (Builder $query) use ($esSuperAdmin, $usuarioActual) {
                            if (!$esSuperAdmin && $usuarioActual->sucursal_id) {
                                $query->where('sucursal_id', $usuarioActual->sucursal_id);
                            }
                        }
                    )
                    ->searchable()
                    ->preload(),
                Forms\Components\Textarea::make('descripcion')
                    ->maxLength(65535),
                Forms\Components\Toggle::make('activo')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        $usuarioActual = auth()->user();
        $esSuperAdmin = $usuarioActual->is_super_admin || $usuarioActual->hasRole('Super Admin');

        return $table
            ->modifyQueryUsing(function (Builder $query) use ($usuarioActual, $esSuperAdmin) {
                if (!$esSuperAdmin && $usuarioActual->sucursal_id) {
                    $query->whereHas('espacio', function ($query) use ($usuarioActual) {
                        $query->where('sucursal_id', $usuarioActual->sucursal_id);
                    });
                }
            })
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('numero_puestos')
                    ->label('Puestos')
                    ->sortable(),
                Tables\Columns\TextColumn::make('espacio.nombre')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('espacio.sucursal.nombre')
                    ->label('Sucursal')
                    ->sortable()
                    ->searchable()
                    ->visible($esSuperAdmin),
                Tables\Columns\IconColumn::make('activo')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('activo'),
                Tables\Filters\SelectFilter::make('espacio_id')
                    ->label('Espacio')
                    ->relationship('espacio', 'nombre', function (Builder $query) use ($usuarioActual, $esSuperAdmin) {
                        if (!$esSuperAdmin && $usuarioActual->sucursal_id) {
                            $query->where('sucursal_id', $usuarioActual->sucursal_id);
                        }
                    })
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMesas::route('/'),
            'create' => Pages\CreateMesa::route('/create'),
            'edit' => Pages\EditMesa::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $usuarioActual = auth()->user();
        $esSuperAdmin = $usuarioActual->is_super_admin || $usuarioActual->hasRole('Super Admin');

        $query = parent::getEloquentQuery();

        if (!$esSuperAdmin && $usuarioActual->sucursal_id) {
            $query->whereHas('espacio', function ($query) use ($usuarioActual) {
                $query->where('sucursal_id', $usuarioActual->sucursal_id);
            });
        }

        return $query;
    }
}
