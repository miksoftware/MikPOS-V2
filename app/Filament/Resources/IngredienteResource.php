<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IngredienteResource\Pages;
use App\Models\Ingrediente;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class IngredienteResource extends Resource
{
    protected static ?string $model = Ingrediente::class;

    protected static ?string $navigationIcon = 'heroicon-o-beaker';

    protected static ?string $navigationGroup = 'Restaurante';

    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        $user = Auth::user();
        $esSuperAdmin = $user->hasRole('Super Admin'); // Ajusta según la lógica de roles que uses
        $sucursalUsuario = $user->sucursal_id ?? null;

        return $form
            ->schema([
                // Añadimos la selección de sucursal condicionalmente
                Forms\Components\Select::make('sucursal_id')
                    ->label('Sucursal')
                    ->relationship('sucursal', 'nombre')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->visible($esSuperAdmin) // Solo visible para superadmin
                    ->default($sucursalUsuario), // Valor predeterminado si no es superadmin

                Grid::make(3)
                    ->schema([
                        Section::make('Información básica')
                            ->columnSpan(2)
                            ->schema([
                                Forms\Components\TextInput::make('nombre')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\Textarea::make('descripcion')
                                    ->maxLength(65535),
                                Forms\Components\Select::make('unidad_medida_id')
                                    ->relationship('unidadMedida', 'nombre')
                                    ->required()
                                    ->searchable()
                                    ->preload(),
                                Forms\Components\Select::make('area_preparacion_id')
                                    ->relationship('areaPreparacion', 'nombre')
                                    ->required()
                                    ->searchable()
                                    ->preload(),
                            ]),

                        Section::make('Inventario')
                            ->columnSpan(1)
                            ->schema([
                                Forms\Components\TextInput::make('stock_actual')
                                    ->numeric()
                                    ->step('0.01')
                                    ->default(0)
                                    ->suffix(fn($get) => $get('unidad_medida_id') ? optional($get('unidad_medida'))->abreviatura : ''),
                                Forms\Components\TextInput::make('stock_minimo')
                                    ->numeric()
                                    ->step('0.01')
                                    ->default(0)
                                    ->helperText('Se alertará cuando el stock sea menor a este valor'),
                                Forms\Components\Toggle::make('activo')
                                    ->default(true)
                                    ->inline(false),
                            ]),

                        Section::make('Impuestos')
                            ->columnSpan(2)
                            ->schema([
                                Forms\Components\Toggle::make('aplica_impuesto')
                                    ->default(false)
                                    ->reactive()
                                    ->inline(false),
                                Forms\Components\Select::make('impuesto_id')
                                    ->relationship('impuesto', 'nombre')
                                    ->searchable()
                                    ->preload()
                                    ->visible(fn(Get $get): bool => (bool)$get('aplica_impuesto'))
                                    ->required(fn(Get $get): bool => (bool)$get('aplica_impuesto')),
                            ]),

                        Section::make('Descuentos')
                            ->columnSpan(1)
                            ->schema([
                                Forms\Components\Toggle::make('tiene_descuento')
                                    ->default(false)
                                    ->reactive()
                                    ->inline(false),
                                Forms\Components\Select::make('tipo_descuento')
                                    ->options([
                                        'porcentaje' => 'Porcentaje (%)',
                                        'monto' => 'Monto fijo ($)',
                                    ])
                                    ->visible(fn(Get $get): bool => (bool)$get('tiene_descuento'))
                                    ->required(fn(Get $get): bool => (bool)$get('tiene_descuento')),
                                Forms\Components\TextInput::make('valor_descuento')
                                    ->numeric()
                                    ->step('0.01')
                                    ->visible(fn(Get $get): bool => (bool)$get('tiene_descuento'))
                                    ->required(fn(Get $get): bool => (bool)$get('tiene_descuento'))
                                    ->suffix(function (Get $get) {
                                        if ($get('tiene_descuento')) {
                                            return $get('tipo_descuento') === 'porcentaje' ? '%' : '$';
                                        }
                                        return '';
                                    }),
                            ]),

                        Section::make('Precios y Utilidad')
                            ->columnSpan(3)
                            ->schema([
                                Forms\Components\Grid::make(3)
                                    ->schema([
                                        Forms\Components\TextInput::make('precio_compra')
                                            ->numeric()
                                            ->step('0.01')
                                            ->required()
                                            ->prefix('$')
                                            ->reactive()
                                            ->afterStateUpdated(function (Get $get, Set $set) {
                                                self::calcularUtilidad($get, $set);
                                            }),

                                        Forms\Components\TextInput::make('precio_venta')
                                            ->numeric()
                                            ->step('0.01')
                                            ->required()
                                            ->prefix('$')
                                            ->reactive()
                                            ->afterStateUpdated(function (Get $get, Set $set) {
                                                self::calcularUtilidad($get, $set);
                                            }),

                                        Forms\Components\TextInput::make('utilidad_porcentaje')
                                            ->numeric()
                                            ->step('0.01')
                                            ->suffix('%')
                                            ->disabled()
                                            ->dehydrated(),

                                        Forms\Components\TextInput::make('utilidad_monto')
                                            ->numeric()
                                            ->step('0.01')
                                            ->prefix('$')
                                            ->disabled()
                                            ->dehydrated(),
                                    ]),
                            ]),
                    ]),
            ]);
    }

    private static function calcularUtilidad(Get $get, Set $set): void
    {
        $precioCompra = (float)$get('precio_compra');
        $precioVenta = (float)$get('precio_venta');

        if ($precioCompra > 0 && $precioVenta > 0) {
            $utilidadMonto = $precioVenta - $precioCompra;
            $utilidadPorcentaje = ($utilidadMonto / $precioCompra) * 100;

            $set('utilidad_monto', round($utilidadMonto, 2));
            $set('utilidad_porcentaje', round($utilidadPorcentaje, 2));
        } else {
            $set('utilidad_monto', 0);
            $set('utilidad_porcentaje', 0);
        }
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sucursal.nombre')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('nombre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('unidadMedida.nombre')
                    ->label('Unidad')
                    ->sortable(),
                Tables\Columns\TextColumn::make('areaPreparacion.nombre')
                    ->label('Área')
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock_actual')
                    ->label('Stock')
                    ->numeric(2)
                    ->sortable()
                    ->color(fn($record) => $record->stock_actual <= $record->stock_minimo ? 'danger' : 'success'),
                Tables\Columns\TextColumn::make('precio_compra')
                    ->money('COP')
                    ->sortable(),
                Tables\Columns\TextColumn::make('precio_venta')
                    ->money('COP')
                    ->sortable(),
                Tables\Columns\TextColumn::make('utilidad_porcentaje')
                    ->label('Utilidad')
                    ->suffix('%')
                    ->numeric(2)
                    ->sortable(),
                Tables\Columns\IconColumn::make('aplica_impuesto')
                    ->label('Imp.')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\IconColumn::make('tiene_descuento')
                    ->label('Desc.')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\IconColumn::make('activo')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('activo'),
                // Añadimos filtro por sucursal
                Tables\Filters\SelectFilter::make('sucursal_id')
                    ->label('Sucursal')
                    ->relationship('sucursal', 'nombre')
                    ->preload()
                    ->searchable(),
                Tables\Filters\SelectFilter::make('unidad_medida_id')
                    ->label('Unidad de Medida')
                    ->relationship('unidadMedida', 'nombre')
                    ->preload()
                    ->searchable(),
                Tables\Filters\SelectFilter::make('area_preparacion_id')
                    ->label('Área de Preparación')
                    ->relationship('areaPreparacion', 'nombre')
                    ->preload()
                    ->searchable(),
                Tables\Filters\Filter::make('stock_minimo')
                    ->label('Stock bajo mínimo')
                    ->query(fn(Builder $query): Builder => $query->whereColumn('stock_actual', '<=', 'stock_minimo')),
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

    // Método para filtrar los registros según la sucursal del usuario
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = Auth::user();

        // Si no es superadmin y tiene una sucursal asignada, filtrar por su sucursal
        if (!$user->hasRole('super_admin') && $user->sucursal_id) {
            $query->where('sucursal_id', $user->sucursal_id);
        }

        return $query;
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListIngredientes::route('/'),
            'create' => Pages\CreateIngrediente::route('/create'),
            'edit' => Pages\EditIngrediente::route('/{record}/edit'),
        ];
    }
}
