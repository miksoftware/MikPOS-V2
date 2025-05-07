<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompraResource\Pages;
use App\Models\Compra;
use App\Models\Supplier;
use App\Models\Producto;
use App\Models\Ingrediente;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\ToggleButtons;
use Carbon\Carbon;

class CompraResource extends Resource
{
    protected static ?string $model = Compra::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationGroup = 'Inventario';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        // Verificamos si el usuario es superadmin o tiene una sucursal asociada
        $user = Auth::user();
        $esSuperAdmin = $user->hasRole('Super Admin');
        $sucursalUsuario = $user->sucursal_id ?? null;

        return $form->schema([
            Wizard::make([
                Step::make('Información de la compra')
                    ->description('Datos generales de la compra')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                // Selección de sucursal solo para superadmin
                                Forms\Components\Select::make('sucursal_id')
                                    ->label('Sucursal')
                                    ->options(function () {
                                        return \App\Models\Sucursal::query()
                                            ->orderBy('nombre')
                                            ->pluck('nombre', 'id')
                                            ->toArray();
                                    })
                                    ->required()
                                    ->searchable()
                                    ->visible($esSuperAdmin)
                                    ->default($sucursalUsuario)
                                    ->columnSpan(1),

                                Forms\Components\Select::make('proveedor_id')
                                    ->label('Proveedor')
                                    ->options(function () {
                                        return Supplier::query()
                                            ->orderBy('name')
                                            ->pluck('name', 'id')
                                            ->toArray();
                                    })
                                    ->required()
                                    ->searchable()
                                    ->columnSpan(1),

                                Forms\Components\TextInput::make('numero_factura')
                                    ->label('Número de factura')
                                    ->placeholder('Ingrese el número de factura del proveedor')
                                    ->maxLength(50)
                                    ->columnSpan(1),

                                Forms\Components\DatePicker::make('fecha_compra')
                                    ->label('Fecha de compra')
                                    ->required()
                                    ->default(now())
                                    ->maxDate(now())
                                    ->columnSpan(1),

                                Forms\Components\Select::make('estado')
                                    ->label('Estado')
                                    ->options([
                                        'pendiente' => 'Pendiente',
                                        'completada' => 'Completada',
                                        'anulada' => 'Anulada',
                                    ])
                                    ->default('pendiente')
                                    ->required()
                                    ->columnSpan(1),

                                Forms\Components\Hidden::make('usuario_id')
                                    ->default(fn() => Auth::id()),

                                Forms\Components\Textarea::make('observaciones')
                                    ->label('Observaciones')
                                    ->placeholder('Alguna observación adicional sobre la compra')
                                    ->maxLength(500)
                                    ->columnSpan(2),
                            ])
                            ->columnSpan('full'),
                    ]),

                Step::make('Productos e ingredientes')
                    ->description('Añadir productos e ingredientes a la compra')
                    ->icon('heroicon-o-shopping-bag')
                    ->schema([
                        ToggleButtons::make('tipo_item')
                            ->label('Tipo de artículo')
                            ->options([
                                'producto' => 'Productos',
                                'ingrediente' => 'Ingredientes',
                            ])
                            ->default('producto')
                            ->live()
                            ->columnSpan('full'),

                        // Repeater para productos
                        Repeater::make('productos')
                            ->label('Productos')
                            ->schema([
                                Forms\Components\Select::make('producto_id')
                                    ->label('Producto')
                                    ->options(function () use ($sucursalUsuario, $esSuperAdmin) {
                                        $query = Producto::query();

                                        // Filtrar por sucursal
                                        if (!$esSuperAdmin && $sucursalUsuario) {
                                            $query->where('sucursal_id', $sucursalUsuario);
                                        }

                                        return $query->pluck('nombre', 'id')->toArray();
                                    })
                                    ->searchable()
                                    ->required()
                                    ->columnSpan(1)
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, $set) {
                                        if ($state) {
                                            $producto = Producto::find($state);
                                            if ($producto) {
                                                $set('precio_compra_anterior', $producto->precio_costo);
                                                $set('precio_compra_actual', $producto->precio_costo);
                                                $set('precio_venta_anterior', $producto->precio_venta);
                                                $set('precio_venta_nuevo', $producto->precio_venta);
                                                $set('comprable_type', 'App\\Models\\Producto');
                                                $set('comprable_id', $producto->id);
                                            }
                                        }
                                    }),

                                Forms\Components\TextInput::make('cantidad')
                                    ->label('Cantidad')
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(0.01)
                                    ->required()
                                    ->columnSpan(1)
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, $set, $get) {
                                        // Calcular subtotal
                                        $cantidad = (float)$state;
                                        $precioCompra = (float)$get('precio_compra_actual');
                                        $subtotal = $cantidad * $precioCompra;
                                        $set('subtotal', round($subtotal, 2));
                                    }),

                                Forms\Components\TextInput::make('precio_compra_anterior')
                                    ->label('Precio anterior')
                                    ->numeric()
                                    ->prefix('$')
                                    ->disabled()
                                    ->dehydrated()
                                    ->columnSpan(1),

                                Forms\Components\TextInput::make('precio_compra_actual')
                                    ->label('Precio compra')
                                    ->numeric()
                                    ->prefix('$')
                                    ->required()
                                    ->columnSpan(1)
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, $set, $get) {
                                        // Calcula el subtotal
                                        $cantidad = (float)$get('cantidad');
                                        $precioCompra = (float)$state;
                                        $subtotal = $cantidad * $precioCompra;
                                        $set('subtotal', round($subtotal, 2));

                                        // Evalúa el cambio de precio
                                        $precioAnterior = (float)$get('precio_compra_anterior');
                                        if ($precioAnterior > 0) {
                                            if ($precioCompra > $precioAnterior) {
                                                $porcentajeCambio = (($precioCompra - $precioAnterior) / $precioAnterior) * 100;
                                                $set('diferencia_porcentual', round($porcentajeCambio, 2));
                                                $set('indicador_cambio', '⬆️ +' . round($porcentajeCambio, 2) . '%');
                                                $set('color_cambio', 'danger');
                                            } elseif ($precioCompra < $precioAnterior) {
                                                $porcentajeCambio = (($precioAnterior - $precioCompra) / $precioAnterior) * 100;
                                                $set('diferencia_porcentual', -round($porcentajeCambio, 2));
                                                $set('indicador_cambio', '⬇️ -' . round($porcentajeCambio, 2) . '%');
                                                $set('color_cambio', 'success');
                                            } else {
                                                $set('diferencia_porcentual', 0);
                                                $set('indicador_cambio', '- Sin cambio');
                                                $set('color_cambio', 'gray');
                                            }
                                        }
                                    }),

                                Forms\Components\Hidden::make('color_cambio')
                                    ->default('gray'),

                                Forms\Components\Placeholder::make('indicador_precio')
                                    ->label('Cambio')
                                    ->visible(fn(Get $get): bool => $get('precio_compra_anterior') > 0)
                                    ->content(function (Get $get): string {
                                        $indicador = $get('indicador_cambio') ?? '- Sin cambio';
                                        return $indicador; // Retorna solo el porcentaje con el simbolo
                                    })
                                    ->columnSpan(1),

                                Forms\Components\Hidden::make('indicador_cambio')
                                    ->default('-'),

                                Forms\Components\TextInput::make('diferencia_porcentual')
                                    ->label('Variación')
                                    ->numeric()
                                    ->suffix('%')
                                    ->disabled()
                                    ->columnSpan(1),

                                Forms\Components\TextInput::make('precio_venta_anterior')
                                    ->label('Precio venta anterior')
                                    ->numeric()
                                    ->prefix('$')
                                    ->disabled()
                                    ->dehydrated()
                                    ->columnSpan(1),

                                Forms\Components\Toggle::make('actualizar_precio_venta')
                                    ->label('Actualizar precio de venta')
                                    ->default(false)
                                    ->reactive()
                                    ->columnSpan(1),

                                Forms\Components\TextInput::make('precio_venta_nuevo')
                                    ->label('Precio de venta nuevo')
                                    ->numeric()
                                    ->prefix('$')
                                    ->required()
                                    ->visible(fn(Get $get): bool => $get('actualizar_precio_venta'))
                                    ->columnSpan(1),

                                Forms\Components\TextInput::make('subtotal')
                                    ->label('Subtotal')
                                    ->numeric()
                                    ->prefix('$')
                                    ->disabled()
                                    ->dehydrated()
                                    ->columnSpan(1),

                                // Campos ocultos para identificar el tipo
                                Forms\Components\Hidden::make('comprable_type')
                                    ->default('App\\Models\\Producto'),
                                Forms\Components\Hidden::make('comprable_id')
                                    ->default(null),
                            ])
                            ->columns([
                                'default' => 1,
                                'md' => 3,
                                'lg' => 6
                            ])
                            ->defaultItems(0)
                            ->addActionLabel('Añadir producto')
                            ->visible(fn(Get $get): bool => $get('tipo_item') === 'producto')
                            ->collapsed()
                            ->columnSpanFull()
                            ->live()
                            ->afterStateUpdated(function ($state, $set, $livewire) {
                                $totales = self::calcularTotales($livewire->data);
                                $set('../../subtotal', $totales['subtotal']);
                                $set('../../impuestos', $totales['impuestos']);
                                $set('../../total', $totales['total']);
                            })
                            ->itemLabel(fn(array $state): ?string => isset($state['producto_id']) && $state['producto_id'] ?
                                (Producto::find($state['producto_id'])?->nombre ?? 'Producto') .
                                ' - $' . ($state['subtotal'] ?? 0)
                                : null),

                        // Repeater para ingredientes
                        Repeater::make('ingredientes')
                            ->label('Ingredientes')
                            ->schema([
                                Forms\Components\Select::make('ingrediente_id')
                                    ->label('Ingrediente')
                                    ->options(function () use ($sucursalUsuario, $esSuperAdmin) {
                                        $query = Ingrediente::query();

                                        // Filtrar por sucursal
                                        if (!$esSuperAdmin && $sucursalUsuario) {
                                            $query->where('sucursal_id', $sucursalUsuario);
                                        }

                                        return $query->pluck('nombre', 'id')->toArray();
                                    })
                                    ->searchable()
                                    ->required()
                                    ->columnSpan(1)
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, $set) {
                                        if ($state) {
                                            $ingrediente = Ingrediente::find($state);
                                            if ($ingrediente) {
                                                $set('precio_compra_anterior', $ingrediente->precio_compra);
                                                $set('precio_compra_actual', $ingrediente->precio_compra);
                                                $set('comprable_type', 'App\\Models\\Ingrediente');
                                                $set('comprable_id', $ingrediente->id);
                                            }
                                        }
                                    }),

                                Forms\Components\TextInput::make('cantidad')
                                    ->label('Cantidad')
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(0.01)
                                    ->required()
                                    ->columnSpan(1)
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, $set, $get) {
                                        // Calcular subtotal
                                        $cantidad = (float)$state;
                                        $precioCompra = (float)$get('precio_compra_actual');
                                        $subtotal = $cantidad * $precioCompra;
                                        $set('subtotal', round($subtotal, 2));
                                    }),

                                Forms\Components\TextInput::make('precio_compra_anterior')
                                    ->label('Precio anterior')
                                    ->numeric()
                                    ->prefix('$')
                                    ->disabled()
                                    ->dehydrated()
                                    ->columnSpan(1),

                                Forms\Components\TextInput::make('precio_compra_actual')
                                    ->label('Precio compra')
                                    ->numeric()
                                    ->prefix('$')
                                    ->required()
                                    ->columnSpan(1)
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, $set, $get) {
                                        // Calcula el subtotal
                                        $cantidad = (float)$get('cantidad');
                                        $precioCompra = (float)$state;
                                        $subtotal = $cantidad * $precioCompra;
                                        $set('subtotal', round($subtotal, 2));

                                        // Evalúa el cambio de precio
                                        $precioAnterior = (float)$get('precio_compra_anterior');
                                        if ($precioAnterior > 0) {
                                            if ($precioCompra > $precioAnterior) {
                                                $porcentajeCambio = (($precioCompra - $precioAnterior) / $precioAnterior) * 100;
                                                $set('diferencia_porcentual', round($porcentajeCambio, 2));
                                                $set('indicador_cambio', '⬆️ +' . round($porcentajeCambio, 2) . '%');
                                                $set('color_cambio', 'danger');
                                            } elseif ($precioCompra < $precioAnterior) {
                                                $porcentajeCambio = (($precioAnterior - $precioCompra) / $precioAnterior) * 100;
                                                $set('diferencia_porcentual', -round($porcentajeCambio, 2));
                                                $set('indicador_cambio', '⬇️ -' . round($porcentajeCambio, 2) . '%');
                                                $set('color_cambio', 'success');
                                            } else {
                                                $set('diferencia_porcentual', 0);
                                                $set('indicador_cambio', '- Sin cambio');
                                                $set('color_cambio', 'gray');
                                            }
                                        }
                                    }),

                                Forms\Components\Hidden::make('color_cambio')
                                    ->default('gray'),

                                Forms\Components\Placeholder::make('indicador_precio')
                                    ->label('Cambio')
                                    ->visible(fn(Get $get): bool => $get('precio_compra_anterior') > 0)
                                    ->content(function (Get $get): string {
                                        $indicador = $get('indicador_cambio') ?? '- Sin cambio';
                                        return $indicador; // Retorna solo el porcentaje con el simbolo
                                    })
                                    ->columnSpan(1),

                                Forms\Components\Hidden::make('indicador_cambio')
                                    ->default('-'),

                                Forms\Components\TextInput::make('diferencia_porcentual')
                                    ->label('Variación')
                                    ->numeric()
                                    ->suffix('%')
                                    ->disabled()
                                    ->columnSpan(1),

                                Forms\Components\TextInput::make('subtotal')
                                    ->label('Subtotal')
                                    ->numeric()
                                    ->prefix('$')
                                    ->disabled()
                                    ->dehydrated()
                                    ->columnSpan(1),

                                // Campos ocultos para identificar el tipo
                                Forms\Components\Hidden::make('comprable_type')
                                    ->default('App\\Models\\Ingrediente'),
                                Forms\Components\Hidden::make('comprable_id')
                                    ->default(null),
                            ])
                            ->columns([
                                'default' => 1,
                                'md' => 3,
                                'lg' => 6
                            ])
                            ->defaultItems(0)
                            ->addActionLabel('Añadir ingrediente')
                            ->visible(fn(Get $get): bool => $get('tipo_item') === 'ingrediente')
                            ->collapsed()
                            ->columnSpanFull()
                            ->live()
                            ->afterStateUpdated(function ($state, $set, $livewire) {
                                $totales = self::calcularTotales($livewire->data);
                                $set('../../subtotal', $totales['subtotal']);
                                $set('../../impuestos', $totales['impuestos']);
                                $set('../../total', $totales['total']);
                            })
                            ->itemLabel(fn(array $state): ?string => isset($state['ingrediente_id']) && $state['ingrediente_id'] ?
                                (Ingrediente::find($state['ingrediente_id'])?->nombre ?? 'Ingrediente') .
                                ' - $' . ($state['subtotal'] ?? 0)
                                : null),

                        Section::make('Totales')
                            ->schema([
                                Forms\Components\TextInput::make('subtotal')
                                    ->label('Subtotal')
                                    ->numeric()
                                    ->prefix('$')
                                    ->disabled()
                                    ->default(0)
                                    ->dehydrated()
                                    ->columnSpan(1),

                                Forms\Components\TextInput::make('impuestos')
                                    ->label('Impuestos')
                                    ->numeric()
                                    ->prefix('$')
                                    ->disabled()
                                    ->default(0)
                                    ->dehydrated()
                                    ->columnSpan(1),

                                Forms\Components\TextInput::make('total')
                                    ->label('Total')
                                    ->numeric()
                                    ->prefix('$')
                                    ->disabled()
                                    ->default(0)
                                    ->dehydrated()
                                    ->columnSpan(1),
                            ])
                            ->columns(3)
                            ->columnSpanFull(),
                    ]),
            ])
                ->skippable()
                ->columnSpanFull(),
        ]);
    }

    // Método para calcular los totales
    private static function calcularTotales($data)
    {
        $subtotal = 0;
        $impuestos = 0;

        // Calcular subtotal de productos
        if (isset($data['productos']) && is_array($data['productos'])) {
            foreach ($data['productos'] as $producto) {
                if (isset($producto['subtotal'])) {
                    $subtotal += (float)$producto['subtotal'];
                }
            }
        }

        // Calcular subtotal de ingredientes
        if (isset($data['ingredientes']) && is_array($data['ingredientes'])) {
            foreach ($data['ingredientes'] as $ingrediente) {
                if (isset($ingrediente['subtotal'])) {
                    $subtotal += (float)$ingrediente['subtotal'];
                }
            }
        }

        // Calcular impuestos si fuera necesario
        // $impuestos = $subtotal * 0.19; // Ejemplo para IVA 19%

        $total = $subtotal + $impuestos;

        return [
            'subtotal' => round($subtotal, 2),
            'impuestos' => round($impuestos, 2),
            'total' => round($total, 2),
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('sucursal.nombre')
                    ->label('Sucursal')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('proveedor.name') // Cambiado de 'nombre' a 'name'
                ->label('Proveedor')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('numero_factura')
                    ->label('Nº Factura')
                    ->searchable(),

                Tables\Columns\TextColumn::make('fecha_compra')
                    ->label('Fecha')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('total')
                    ->money('COP')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('estado')
                    ->label('Estado')
                    ->colors([
                        'danger' => 'anulada',
                        'warning' => 'pendiente',
                        'success' => 'completada',
                    ]),

                Tables\Columns\TextColumn::make('usuario.name')
                    ->label('Registrado por')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado el')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('sucursal_id')
                    ->label('Sucursal')
                    ->options(function () {
                        return \App\Models\Sucursal::pluck('nombre', 'id')->toArray();
                    })
                    ->visible(fn() => Auth::user()->hasRole('Super Admin'))
                    ->searchable(),

                Tables\Filters\SelectFilter::make('proveedor_id')
                    ->label('Proveedor')
                    ->options(function () {
                        return Supplier::pluck('name', 'id')->toArray();
                    })
                    ->searchable(),

                Tables\Filters\SelectFilter::make('estado')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'completada' => 'Completada',
                        'anulada' => 'Anulada',
                    ]),

                Tables\Filters\Filter::make('fecha_compra')
                    ->form([
                        Forms\Components\DatePicker::make('desde')
                            ->label('Desde'),
                        Forms\Components\DatePicker::make('hasta')
                            ->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['desde'],
                                fn(Builder $query, $date): Builder => $query->whereDate('fecha_compra', '>=', $date),
                            )
                            ->when(
                                $data['hasta'],
                                fn(Builder $query, $date): Builder => $query->whereDate('fecha_compra', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('finalizar')
                    ->label('Finalizar')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn(Compra $record) => $record->estado === 'pendiente')
                    ->action(function (Compra $record) {
                        // Actualizamos el estado de la compra
                        $record->estado = 'completada';
                        $record->save();

                        // Actualizamos el stock y los precios de los productos e ingredientes
                        foreach ($record->detalles as $detalle) {
                            $comprable = $detalle->comprable;

                            if ($comprable) {
                                if ($comprable instanceof Producto) {
                                    // Actualizar stock del producto
                                    if ($comprable->controlar_stock) {
                                        $comprable->stock_actual += $detalle->cantidad;
                                    }

                                    // Actualizar precio de costo
                                    $comprable->precio_costo = $detalle->precio_compra_actual;

                                    // Actualizar precio de venta si se indicó
                                    if ($detalle->actualizar_precio_venta) {
                                        $comprable->precio_venta = $detalle->precio_venta_nuevo;
                                    }

                                    $comprable->save();
                                } elseif ($comprable instanceof Ingrediente) {
                                    // Actualizar stock del ingrediente
                                    $comprable->stock_actual += $detalle->cantidad;

                                    // Actualizar precio de compra
                                    $comprable->precio_compra = $detalle->precio_compra_actual;

                                    $comprable->save();
                                }
                            }
                        }

                        // Mensaje de éxito
                        Filament\Notifications\Notification::make()
                            ->title('Compra finalizada')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('anular')
                    ->label('Anular')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn(Compra $record) => $record->estado === 'pendiente')
                    ->action(function (Compra $record) {
                        $record->estado = 'anulada';
                        $record->save();

                        Filament\Notifications\Notification::make()
                            ->title('Compra anulada')
                            ->warning()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCompras::route('/'),
            'create' => Pages\CreateCompra::route('/create'),
            'edit' => Pages\EditCompra::route('/{record}/edit'),
        ];
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
}
