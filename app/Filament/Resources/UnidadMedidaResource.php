<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UnidadMedidaResource\Pages;
use App\Models\UnidadMedida;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UnidadMedidaResource extends Resource
{
    protected static ?string $model = UnidadMedida::class;

    protected static ?string $navigationIcon = 'heroicon-o-scale';

    protected static ?string $navigationGroup = 'Configuración';

    protected static ?int $navigationSort = 30;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('abreviatura')
                    ->maxLength(255),
                Forms\Components\Select::make('tipo')
                    ->options([
                        'peso' => 'Peso',
                        'volumen' => 'Volumen',
                        'longitud' => 'Longitud',
                        'unidad' => 'Unidad',
                        'otro' => 'Otro',
                    ])
                    ->default('otro')
                    ->required(),
                Forms\Components\Textarea::make('descripcion')
                    ->maxLength(65535),
                Forms\Components\Toggle::make('activo')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('abreviatura')
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('tipo')
                    ->colors([
                        'primary' => 'peso',
                        'success' => 'volumen',
                        'warning' => 'longitud',
                        'info' => 'unidad',
                        'secondary' => 'otro',
                    ]),
                Tables\Columns\IconColumn::make('activo')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('activo'),
                Tables\Filters\SelectFilter::make('tipo')
                    ->options([
                        'peso' => 'Peso',
                        'volumen' => 'Volumen',
                        'longitud' => 'Longitud',
                        'unidad' => 'Unidad',
                        'otro' => 'Otro',
                    ]),
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
            'index' => Pages\ListUnidadMedidas::route('/'),
            'create' => Pages\CreateUnidadMedida::route('/create'),
            'edit' => Pages\EditUnidadMedida::route('/{record}/edit'),
        ];
    }
}
