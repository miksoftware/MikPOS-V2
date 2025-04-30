<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SucursalResource\Pages;
use App\Models\Sucursal;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;

class SucursalResource extends Resource
{
    protected static ?string $model = Sucursal::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    protected static ?string $navigationGroup = 'Configuración';
    protected static ?int $navigationSort = 3;
    protected static ?string $modelLabel = 'Sucursal';
    protected static ?string $pluralModelLabel = 'Sucursales';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información General')
                    ->description('Datos principales de la sucursal')
                    ->schema([
                        Forms\Components\TextInput::make('nombre')
                            ->label('Nombre de la sucursal')
                            ->required()
                            ->placeholder('Ej: Sucursal Central')
                            ->maxLength(255),

                        Forms\Components\Select::make('empresa_id')
                            ->relationship('empresa', 'razon_social')
                            ->label('Empresa')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Toggle::make('activo')
                            ->label('Sucursal activa')
                            ->helperText('Determina si la sucursal está operativa')
                            ->required()
                            ->default(true),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Ubicación')
                    ->description('Localización geográfica de la sucursal')
                    ->schema([
                        Forms\Components\Select::make('departamento_id')
                            ->relationship('departamento', 'nombre')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn(Set $set) => $set('municipio_id', null)),

                        Forms\Components\Select::make('municipio_id')
                            ->relationship('municipio', 'nombre', function (Builder $query, Get $get) {
                                $departamentoId = $get('departamento_id');
                                if (!$departamentoId) {
                                    return $query->whereRaw('1 = 0'); // No mostrar municipios si no hay departamento
                                }
                                return $query->where('departamento_id', $departamentoId);
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->disabled(fn(Get $get): bool => blank($get('departamento_id')))
                            ->helperText(fn(Get $get): string => blank($get('departamento_id'))
                                ? 'Primero seleccione un departamento'
                                : 'Municipios disponibles'
                            ),

                        Forms\Components\Textarea::make('direccion')
                            ->label('Dirección completa')
                            ->required()
                            ->placeholder('Ej: Calle 123 #45-67, Edificio ABC')
                            ->rows(2)
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Detalles de contacto')
                    ->description('Información de contacto de la sucursal')
                    ->schema([
                        Forms\Components\TextInput::make('telefono')
                            ->label('Teléfono')
                            ->tel()
                            ->placeholder('3001234567')
                            ->maxLength(15),

                        Forms\Components\Select::make('encargado_id')
                            ->relationship('encargado', 'nombre')
                            ->label('Encargado')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->createOptionForm([
                                Forms\Components\Grid::make()
                                    ->schema([
                                        Forms\Components\TextInput::make('nombre')
                                            ->label('Nombre completo')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('telefono')
                                            ->label('Teléfono')
                                            ->required()
                                            ->tel()
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('numero_documento')
                                            ->label('Número de documento')
                                            ->required()
                                            ->maxLength(255)
                                            ->unique('encargados', 'numero_documento'),
                                    ])
                                    ->columns(2)
                            ]),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('empresa.razon_social')
                    ->label('Empresa')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('departamento.nombre')
                    ->label('Departamento')
                    ->placeholder('Sin departamento')
                    ->searchable(),

                Tables\Columns\TextColumn::make('municipio.nombre')
                    ->label('Municipio')
                    ->placeholder('Sin municipio')
                    ->searchable(),

                Tables\Columns\TextColumn::make('telefono')
                    ->label('Teléfono')
                    ->searchable(),

                Tables\Columns\TextColumn::make('encargado.nombre')
                    ->label('Encargado')
                    ->searchable(),

                Tables\Columns\IconColumn::make('activo')
                    ->label('Estado')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizada')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort('updated_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('empresa_id')
                    ->label('Empresa')
                    ->relationship('empresa', 'razon_social')
                    ->multiple()
                    ->preload(),

                Tables\Filters\SelectFilter::make('departamento_id')
                    ->label('Departamento')
                    ->relationship('departamento', 'nombre')
                    ->multiple()
                    ->preload(),

                Tables\Filters\SelectFilter::make('municipio_id')
                    ->label('Municipio')
                    ->relationship('municipio', 'nombre')
                    ->multiple()
                    ->preload(),

                Tables\Filters\TernaryFilter::make('activo')
                    ->label('Estado')
                    ->placeholder('Todas')
                    ->trueLabel('Solo activas')
                    ->falseLabel('Solo inactivas'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
                        ->requiresConfirmation(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('activar')
                        ->label('Activar sucursales')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            foreach ($records as $record) {
                                $record->update(['activo' => true]);
                            }
                        }),
                    Tables\Actions\BulkAction::make('desactivar')
                        ->label('Desactivar sucursales')
                        ->icon('heroicon-o-x-mark')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            foreach ($records as $record) {
                                $record->update(['activo' => false]);
                            }
                        }),
                ]),
            ])
            ->emptyStateIcon('heroicon-o-building-storefront')
            ->emptyStateHeading('No hay sucursales registradas')
            ->emptyStateDescription('Aquí aparecerán las sucursales cuando las crees')
            ->emptyStateActions([
                Tables\Actions\Action::make('crear')
                    ->label('Crear sucursal')
                    ->url(fn(): string => SucursalResource::getUrl('create'))
                    ->icon('heroicon-m-plus')
                    ->button(),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = Auth::user();

        // Super admin y Administradores ven todas las sucursales
        if ($user->is_super_admin || $user->hasRole(['Super Admin', 'Administrador'])) {
            return $query;
        }

        // Usuarios normales solo ven su sucursal
        return $query->where('id', $user->sucursal_id);
    }

    public static function getRelations(): array
    {
        return [
            // Puedes añadir relaciones aquí si necesitas
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSucursals::route('/'),
            'create' => Pages\CreateSucursal::route('/create'),
            'edit' => Pages\EditSucursal::route('/{record}/edit'),
        ];
    }

    // Método para personalizar el banner de navegación
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    // Color del badge según cantidad de sucursales
    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::count() > 5 ? 'success' : 'primary';
    }
}
