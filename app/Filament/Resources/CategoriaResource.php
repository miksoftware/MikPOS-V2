<?php

    namespace App\Filament\Resources;

    use App\Filament\Resources\CategoriaResource\Pages;
    use App\Models\Categoria;
    use Filament\Forms;
    use Filament\Forms\Form;
    use Filament\Resources\Resource;
    use Filament\Tables;
    use Filament\Tables\Table;
    use Illuminate\Database\Eloquent\Builder;

    class CategoriaResource extends Resource
    {
        protected static ?string $model = Categoria::class;

        protected static ?string $navigationIcon = 'heroicon-o-tag';

        protected static ?string $navigationGroup = 'Restaurante';

        protected static ?int $navigationSort = 20;

        public static function form(Form $form): Form
        {
            return $form
                ->schema([
                    Forms\Components\TextInput::make('nombre')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\Textarea::make('descripcion')
                        ->maxLength(65535),
                    Forms\Components\Select::make('categoria_id')
                        ->label('Categoría padre')
                        ->relationship(
                            name: 'padre',
                            titleAttribute: 'nombre',
                            modifyQueryUsing: fn (Builder $query, $record) =>
                                $query->when(
                                    $record?->exists,
                                    fn ($q) => $q->where('id', '!=', $record->id)
                                               ->whereNotIn('id', $record->subcategorias->pluck('id')->toArray())
                                )
                        )
                        ->searchable()
                        ->preload()
                        ->placeholder('Seleccione una categoría padre')
                        ->createOptionForm([
                            Forms\Components\TextInput::make('nombre')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\Textarea::make('descripcion')
                                ->maxLength(65535),
                            Forms\Components\Toggle::make('activo')
                                ->default(true),
                        ]),
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
                    Tables\Columns\TextColumn::make('padre.nombre')
                        ->label('Categoría padre')
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
                    Tables\Filters\SelectFilter::make('categoria_id')
                        ->label('Categoría padre')
                        ->relationship('padre', 'nombre')
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
                'index' => Pages\ListCategorias::route('/'),
                'create' => Pages\CreateCategoria::route('/create'),
                'edit' => Pages\EditCategoria::route('/{record}/edit'),
            ];
        }
    }
