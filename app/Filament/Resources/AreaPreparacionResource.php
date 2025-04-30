<?php

    namespace App\Filament\Resources;

    use App\Filament\Resources\AreaPreparacionResource\Pages;
    use App\Models\AreaPreparacion;
    use Filament\Forms;
    use Filament\Forms\Form;
    use Filament\Resources\Resource;
    use Filament\Tables;
    use Filament\Tables\Table;
    use Illuminate\Database\Eloquent\Builder;

    class AreaPreparacionResource extends Resource
    {
        protected static ?string $model = AreaPreparacion::class;
        protected static ?string $navigationIcon = 'heroicon-o-fire';
        protected static ?string $navigationLabel = 'Áreas de Preparación';
        protected static ?string $navigationGroup = 'Restaurante';
        protected static ?string $modelLabel = 'Área de Preparación';
        protected static ?string $pluralModelLabel = 'Áreas de Preparación';

        public static function form(Form $form): Form
        {
            return $form
                ->schema([
                    Forms\Components\TextInput::make('nombre')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\Textarea::make('descripcion')
                        ->maxLength(65535)
                        ->columnSpanFull(),
                    Forms\Components\Toggle::make('activo')
                        ->default(true),
                ]);
        }

        public static function table(Table $table): Table
        {
            return $table
                ->columns([
                    Tables\Columns\TextColumn::make('nombre')
                        ->searchable(),
                    Tables\Columns\TextColumn::make('descripcion')
                        ->limit(50)
                        ->searchable(),
                    Tables\Columns\IconColumn::make('activo')
                        ->boolean(),
                    Tables\Columns\TextColumn::make('created_at')
                        ->dateTime()
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true),
                    Tables\Columns\TextColumn::make('updated_at')
                        ->dateTime()
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true),
                ])
                ->filters([
                    Tables\Filters\SelectFilter::make('activo')
                        ->options([
                            true => 'Activo',
                            false => 'Inactivo',
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

        public static function getRelations(): array
        {
            return [
                //
            ];
        }

        public static function getPages(): array
        {
            return [
                'index' => Pages\ListAreaPreparaciones::route('/'),
                'create' => Pages\CreateAreaPreparacion::route('/create'),
                'edit' => Pages\EditAreaPreparacion::route('/{record}/edit'),
            ];
        }
    }
