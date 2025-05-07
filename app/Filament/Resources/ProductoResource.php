<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductoResource\Pages;
use App\Models\Producto;
use App\Models\Ingrediente;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ProductoResource extends Resource
{
    protected static ?string $model = Producto::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationGroup = 'Restaurante';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        // Verificamos si el usuario es superadmin o tiene una sucursal asociada
        $user = Auth::user();
        $esSuperAdmin = $user->hasRole('Super Admin');
        $sucursalUsuario = $user->sucursal_id ?? null;

        return $form
            ->schema([
                // Selección de sucursal solo para superadmin
                Forms\Components\Select::make('sucursal_id')
                    ->label('Sucursal')
                    ->relationship('sucursal', 'nombre')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->visible($esSuperAdmin)
                    ->default($sucursalUsuario),

                Tabs::make('Información del Producto')
                    ->tabs([
                        Tabs\Tab::make('Información Básica')
                            ->schema([
                                Grid::make(3)
                                    ->schema([
                                        Section::make('Detalles del producto')
                                            ->columnSpan(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('nombre')
                                                    ->required()
                                                    ->maxLength(255),
                                                Forms\Components\Textarea::make('descripcion')
                                                    ->maxLength(65535),
                                                Forms\Components\Select::make('categoria_id')
                                                    ->relationship('categoria', 'nombre')
                                                    ->required()
                                                    ->searchable()
                                                    ->preload(),
                                                Forms\Components\Select::make('tipo_inventario')
                                                    ->label('Tipo de producto')
                                                    ->options([
                                                        'producto_terminado' => 'Producto terminado (con stock)',
                                                        'elaborado_bajo_pedido' => 'Elaborado bajo pedido (consumen ingredientes)',
                                                        'produccion_limitada' => 'Producción limitada por día',
                                                        'combo' => 'Combo (agrupación de productos)',
                                                    ])
                                                    ->required()
                                                    ->default('elaborado_bajo_pedido')
                                                    ->reactive()
                                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                                        if ($get('tipo_inventario') === 'elaborado_bajo_pedido') {
                                                            $set('compuesto', true);
                                                            $set('controlar_stock', false);
                                                        } elseif ($get('tipo_inventario') === 'combo') {
                                                            $set('compuesto', false);
                                                            $set('controlar_stock', false);
                                                        }
                                                    }),
                                                Toggle::make('compuesto')
                                                    ->label('¿Producto compuesto por ingredientes?')
                                                    ->reactive()
                                                    ->default(false)
                                                    ->disabled(fn(Get $get) => $get('tipo_inventario') === 'combo')
                                                    ->helperText('Activa esta opción si este producto se compone de ingredientes específicos.'),
                                                Toggle::make('controlar_stock')
                                                    ->label('¿Controlar stock?')
                                                    ->default(true)
                                                    ->disabled(fn(Get $get) => $get('tipo_inventario') === 'combo')
                                                    ->helperText('Activa esta opción si deseas controlar el inventario para este producto.'),
                                            ]),

                                        Section::make('Estado')
                                            ->columnSpan(1)
                                            ->schema([
                                                Forms\Components\Toggle::make('activo')
                                                    ->default(true)
                                                    ->inline(false),
                                            ]),
                                    ]),
                            ]),

                        Tabs\Tab::make('Inventario')
                            ->schema([
                                Section::make('Control de inventario')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('stock_actual')
                                                    ->label('Stock actual')
                                                    ->numeric()
                                                    ->step('0.01')
                                                    ->default(0)
                                                    ->visible(fn(Get $get): bool => $get('tipo_inventario') === 'producto_terminado' ||
                                                        $get('tipo_inventario') === 'produccion_limitada'),

                                                Forms\Components\TextInput::make('stock_minimo')
                                                    ->label('Stock mínimo')
                                                    ->numeric()
                                                    ->step('0.01')
                                                    ->default(0)
                                                    ->visible(fn(Get $get): bool => $get('tipo_inventario') === 'producto_terminado'),

                                                Forms\Components\TextInput::make('produccion_diaria')
                                                    ->label('Producción diaria máxima')
                                                    ->helperText('Cantidad máxima que se produce cada día')
                                                    ->numeric()
                                                    ->step('0.01')
                                                    ->default(0)
                                                    ->visible(fn(Get $get): bool => $get('tipo_inventario') === 'produccion_limitada'),
                                            ]),
                                    ]),
                            ]),

                        Tabs\Tab::make('Precios')
                            ->schema([
                                Section::make('Impuestos')
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
                                            ->suffix(fn(Get $get) => $get('tipo_descuento') === 'porcentaje' ? '%' : '$'),
                                    ]),

                                Section::make('Precios y Utilidad')
                                    ->schema([
                                        Forms\Components\Grid::make(3)
                                            ->schema([
                                                Forms\Components\TextInput::make('precio_costo')
                                                    ->label(fn(Get $get) => $get('compuesto') || $get('tipo_inventario') === 'combo' ? 'Costo (calculado)' : 'Precio de costo')
                                                    ->numeric()
                                                    ->step('0.01')
                                                    ->required()
                                                    ->prefix('$')
                                                    ->disabled(fn(Get $get) => (bool)$get('compuesto') || $get('tipo_inventario') === 'combo')
                                                    ->dehydrated()
                                                    ->reactive()
                                                    ->afterStateUpdated(function ($state, $livewire) {
                                                        self::calcularUtilidad(
                                                            fn($field) => $livewire->data[$field] ?? null,
                                                            fn($field, $value) => $livewire->data[$field] = $value
                                                        );
                                                    }),

                                                Forms\Components\TextInput::make('precio_venta')
                                                    ->numeric()
                                                    ->step('0.01')
                                                    ->required()
                                                    ->prefix('$')
                                                    ->reactive()
                                                    ->afterStateUpdated(function ($state, $livewire) {
                                                        self::calcularUtilidad(
                                                            fn($field) => $livewire->data[$field] ?? null,
                                                            fn($field, $value) => $livewire->data[$field] = $value
                                                        );
                                                    }),

                                                Forms\Components\TextInput::make('utilidad_porcentaje')
                                                    ->label('Utilidad (%)')
                                                    ->numeric()
                                                    ->step('0.01')
                                                    ->suffix('%')
                                                    ->disabled()
                                                    ->dehydrated(),

                                                Forms\Components\TextInput::make('utilidad_monto')
                                                    ->label('Utilidad ($)')
                                                    ->numeric()
                                                    ->step('0.01')
                                                    ->prefix('$')
                                                    ->disabled()
                                                    ->dehydrated(),
                                            ]),
                                    ]),
                            ]),

                        Tabs\Tab::make('Composición')
                            ->visible(fn(Get $get): bool => (bool)$get('compuesto'))
                            ->schema([
                                Section::make('Composición del producto')
                                    ->schema([
                                        Forms\Components\Repeater::make('ingredientes')
                                            ->schema([
                                                Forms\Components\Select::make('ingrediente_id')
                                                    ->label('Ingrediente')
                                                    ->options(Ingrediente::pluck('nombre', 'id')->toArray())
                                                    ->required()
                                                    ->searchable()
                                                    ->reactive()
                                                    ->columnSpan(1),
                                                Forms\Components\TextInput::make('cantidad')
                                                    ->label('Cantidad')
                                                    ->required()
                                                    ->numeric()
                                                    ->step('0.001')
                                                    ->default(1)
                                                    ->columnSpan(1)
                                                    ->reactive()
                                                    ->afterStateUpdated(fn($state, $livewire) => self::calcularCostoProducto($livewire)),
                                            ])
                                            ->columns(2)
                                            ->defaultItems(0)
                                            ->addActionLabel('Agregar ingrediente')
                                            ->reorderable(false)
                                            ->columnSpanFull()
                                            ->live()
                                            ->afterStateUpdated(fn($state, $livewire) => self::calcularCostoProducto($livewire)),
                                    ]),
                            ]),

                        Tabs\Tab::make('Combo')
                            ->visible(fn(Get $get): bool => $get('tipo_inventario') === 'combo')
                            ->schema([
                                Section::make('Productos del combo')
                                    ->schema([
                                        Forms\Components\Repeater::make('productos_combo')
                                            ->relationship('productosCombo')
                                            ->schema([
                                                Forms\Components\Select::make('producto_id')
                                                    ->label('Producto')
                                                    ->options(function () {
                                                        return Producto::where('tipo_inventario', '!=', 'combo')
                                                            ->pluck('nombre', 'id')
                                                            ->toArray();
                                                    })
                                                    ->required()
                                                    ->searchable()
                                                    ->reactive()
                                                    ->columnSpan(1),
                                                Forms\Components\TextInput::make('cantidad')
                                                    ->label('Cantidad')
                                                    ->required()
                                                    ->numeric()
                                                    ->minValue(1)
                                                    ->default(1)
                                                    ->columnSpan(1)
                                                    ->reactive()
                                                    ->afterStateUpdated(fn($state, $livewire) => self::calcularCostoCombo($livewire)),
                                            ])
                                            ->columns(2)
                                            ->defaultItems(0)
                                            ->addActionLabel('Agregar producto al combo')
                                            ->reorderable(false)
                                            ->columnSpanFull()
                                            ->live()
                                            ->afterStateUpdated(fn($state, $livewire) => self::calcularCostoCombo($livewire)),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    // Método para calcular la utilidad
    private static function calcularUtilidad($getField, $setField): void
    {
        $precioCosto = (float)$getField('precio_costo');
        $precioVenta = (float)$getField('precio_venta');

        if ($precioCosto > 0 && $precioVenta > 0) {
            $utilidadMonto = $precioVenta - $precioCosto;
            $utilidadPorcentaje = ($utilidadMonto / $precioCosto) * 100;

            $setField('utilidad_monto', round($utilidadMonto, 2));
            $setField('utilidad_porcentaje', round($utilidadPorcentaje, 2));
        } else {
            $setField('utilidad_monto', 0);
            $setField('utilidad_porcentaje', 0);
        }
    }

    // Método para calcular el costo de un producto compuesto
    private static function calcularCostoProducto($livewire): void
    {
        if (!isset($livewire->data['ingredientes']) || empty($livewire->data['ingredientes'])) {
            $livewire->data['precio_costo'] = 0;
            self::calcularUtilidad(
                fn($field) => $livewire->data[$field] ?? null,
                fn($field, $value) => $livewire->data[$field] = $value
            );
            return;
        }

        $costoTotal = 0;

        foreach ($livewire->data['ingredientes'] as $item) {
            if (!isset($item['ingrediente_id']) || !isset($item['cantidad'])) {
                continue;
            }

            $ingrediente = Ingrediente::find($item['ingrediente_id']);
            if ($ingrediente) {
                $costoTotal += $ingrediente->precio_compra * (float)$item['cantidad'];
            }
        }

        $livewire->data['precio_costo'] = round($costoTotal, 2);

        self::calcularUtilidad(
            fn($field) => $livewire->data[$field] ?? null,
            fn($field, $value) => $livewire->data[$field] = $value
        );
    }

    // Método para calcular el costo de un combo
    private static function calcularCostoCombo($livewire): void
    {
        if (!isset($livewire->data['productos_combo']) || empty($livewire->data['productos_combo'])) {
            $livewire->data['precio_costo'] = 0;
            self::calcularUtilidad(
                fn($field) => $livewire->data[$field] ?? null,
                fn($field, $value) => $livewire->data[$field] = $value
            );
            return;
        }

        $costoTotal = 0;

        foreach ($livewire->data['productos_combo'] as $item) {
            if (!isset($item['producto_id']) || !isset($item['cantidad'])) {
                continue;
            }

            $producto = Producto::find($item['producto_id']);
            if ($producto) {
                $costoTotal += $producto->precio_costo * (int)$item['cantidad'];
            }
        }

        $livewire->data['precio_costo'] = round($costoTotal, 2);

        self::calcularUtilidad(
            fn($field) => $livewire->data[$field] ?? null,
            fn($field, $value) => $livewire->data[$field] = $value
        );
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
                Tables\Columns\TextColumn::make('categoria.nombre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tipo_inventario')
                    ->badge()
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            'producto_terminado' => 'Terminado',
                            'elaborado_bajo_pedido' => 'Bajo pedido',
                            'produccion_limitada' => 'Producción limitada',
                            'combo' => 'Combo',
                            default => $state,
                        };
                    })
                    ->color(function ($state) {
                        return match ($state) {
                            'producto_terminado' => 'success',
                            'elaborado_bajo_pedido' => 'info',
                            'produccion_limitada' => 'warning',
                            'combo' => 'purple',
                            default => 'gray',
                        };
                    }),
                Tables\Columns\IconColumn::make('compuesto')
                    ->label('Compuesto')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock_actual')
                    ->numeric(2)
                    ->sortable()
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('precio_costo')
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
                Tables\Columns\IconColumn::make('activo')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('activo'),
                Tables\Filters\SelectFilter::make('tipo_inventario')
                    ->label('Tipo de producto')
                    ->options([
                        'producto_terminado' => 'Producto terminado',
                        'elaborado_bajo_pedido' => 'Elaborado bajo pedido',
                        'produccion_limitada' => 'Producción limitada',
                        'combo' => 'Combo',
                    ]),
                Tables\Filters\SelectFilter::make('sucursal_id')
                    ->label('Sucursal')
                    ->relationship('sucursal', 'nombre')
                    ->preload()
                    ->searchable(),
                Tables\Filters\SelectFilter::make('categoria_id')
                    ->label('Categoría')
                    ->relationship('categoria', 'nombre')
                    ->preload()
                    ->searchable(),
                Tables\Filters\TernaryFilter::make('compuesto'),
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
        if (!$user->hasRole('Super Admin') && $user->sucursal_id) {
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
            'index' => Pages\ListProductos::route('/'),
            'create' => Pages\CreateProducto::route('/create'),
            'edit' => Pages\EditProducto::route('/{record}/edit'),
        ];
    }
}
