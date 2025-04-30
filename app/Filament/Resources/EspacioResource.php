<?php

    namespace App\Filament\Resources;

    use App\Filament\Resources\EspacioResource\Pages;
    use App\Models\Espacio;
    use App\Models\Sucursal;
    use Filament\Forms;
    use Filament\Forms\Form;
    use Filament\Resources\Resource;
    use Filament\Tables;
    use Filament\Tables\Table;
    use Illuminate\Database\Eloquent\Builder;

    class EspacioResource extends Resource
    {
        protected static ?string $model = Espacio::class;

        protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

        protected static ?string $navigationGroup = 'Restaurante';

        protected static ?int $navigationSort = 10;

        public static function form(Form $form): Form
        {
            $usuarioActual = auth()->user();
            $esSuperAdmin = $usuarioActual->is_super_admin || $usuarioActual->hasRole('Super Admin');

            return $form
                ->schema([
                    Forms\Components\TextInput::make('nombre')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\Textarea::make('descripcion')
                        ->maxLength(65535),
                    Forms\Components\Select::make('sucursal_id')
                        ->label('Sucursal')
                        ->required()
                        ->relationship(
                            name: 'sucursal',
                            titleAttribute: 'nombre'
                        )
                        ->searchable()
                        ->preload()
                        ->visible($esSuperAdmin)
                        ->default(fn () => $esSuperAdmin ? null : $usuarioActual->sucursal_id),
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
                        $query->where('sucursal_id', $usuarioActual->sucursal_id);
                    }
                })
                ->columns([
                    Tables\Columns\TextColumn::make('nombre')
                        ->searchable()
                        ->sortable(),
                    Tables\Columns\TextColumn::make('sucursal.nombre')
                        ->sortable()
                        ->searchable(),
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
                    Tables\Filters\SelectFilter::make('sucursal_id')
                        ->label('Sucursal')
                        ->relationship('sucursal', 'nombre')
                        ->searchable()
                        ->preload()
                        ->visible($esSuperAdmin),
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
                'index' => Pages\ListEspacios::route('/'),
                'create' => Pages\CreateEspacio::route('/create'),
                'edit' => Pages\EditEspacio::route('/{record}/edit'),
            ];
        }

        public static function getEloquentQuery(): Builder
        {
            $usuarioActual = auth()->user();
            $esSuperAdmin = $usuarioActual->is_super_admin || $usuarioActual->hasRole('Super Admin');

            $query = parent::getEloquentQuery();

            if (!$esSuperAdmin && $usuarioActual->sucursal_id) {
                $query->where('sucursal_id', $usuarioActual->sucursal_id);
            }

            return $query;
        }
    }
