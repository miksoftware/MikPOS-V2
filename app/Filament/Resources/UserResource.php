<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Section;
use Filament\Forms\Get;
use Filament\Forms\Set;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Configuración';
    protected static ?string $modelLabel = 'Usuario';
    protected static ?string $pluralModelLabel = 'Usuarios';
    protected static ?int $navigationSort = 0;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información Personal')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label('Correo Electrónico')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('password')
                            ->label('Contraseña')
                            ->password()
                            ->dehydrateStateUsing(fn($state) => filled($state) ? Hash::make($state) : null)
                            ->dehydrated(fn($state) => filled($state))
                            ->required(fn(string $operation): bool => $operation === 'create'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Asignación & Permisos')
                    ->schema([
                        Forms\Components\Toggle::make('is_super_admin')
                            ->label('Admin Global')
                            ->helperText('Puede ver y gestionar todas las sucursales')
                            ->visible(fn() => Auth::user()->is_super_admin || Auth::user()->hasRole('Super Admin'))
                            ->reactive()
                            ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                if ($state) {
                                    // Si es super admin, eliminamos la sucursal
                                    $set('sucursal_id', null);
                                }
                            }),

                        Forms\Components\Select::make('sucursal_id')
                            ->label('Sucursal')
                            ->relationship('sucursal', 'nombre')
                            ->searchable()
                            ->preload()
                            ->required(fn(Get $get): bool => !$get('is_super_admin'))
                            ->disabled(fn(Get $get): bool => $get('is_super_admin'))
                            ->helperText(fn(Get $get): string => $get('is_super_admin')
                                ? 'Los admin globales tienen acceso a todas las sucursales'
                                : 'Seleccione la sucursal a la que pertenece este usuario'
                            ),

                        Forms\Components\Select::make('roles')
                            ->label('Rol')
                            ->relationship('roles', 'name')
                            ->preload()
                            ->searchable(),
                    ])
                    ->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Correo Electrónico')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sucursal.nombre')
                    ->label('Sucursal')
                    ->sortable()
                    ->placeholder('Todas las sucursales'),
                Tables\Columns\IconColumn::make('is_super_admin')
                    ->label('Admin Global')
                    ->boolean()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Roles')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Super Admin' => 'primary',
                        'Administrador' => 'success',
                        default => 'secondary',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('sucursal_id')
                    ->label('Sucursal')
                    ->relationship('sucursal', 'nombre'),
                Tables\Filters\SelectFilter::make('roles')
                    ->label('Rol')
                    ->relationship('roles', 'name'),
                Tables\Filters\TernaryFilter::make('is_super_admin')
                    ->label('Admin Global'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (Auth::user()->is_super_admin || Auth::user()->hasRole(['Super Admin', 'Administrador'])) {
            return $query;
        }

        // Usuarios normales solo se ven a sí mismos
        return $query->where('id', Auth::id());
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
