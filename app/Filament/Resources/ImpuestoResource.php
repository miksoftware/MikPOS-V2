<?php

    namespace App\Filament\Resources;

    use App\Filament\Resources\ImpuestoResource\Pages;
    use App\Models\Impuesto;
    use Filament\Forms;
    use Filament\Forms\Form;
    use Filament\Resources\Resource;
    use Filament\Tables;
    use Filament\Tables\Table;

    class ImpuestoResource extends Resource
    {
        protected static ?string $model = Impuesto::class;

        protected static ?string $navigationIcon = 'heroicon-o-calculator';

        protected static ?string $navigationGroup = 'Configuración';

        protected static ?int $navigationSort = 15;

        protected static ?string $modelLabel = 'Impuesto';

        protected static ?string $pluralModelLabel = 'Impuestos';

        public static function form(Form $form): Form
        {
            return $form
                ->schema([
                    Forms\Components\TextInput::make('nombre')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('porcentaje')
                        ->required()
                        ->numeric()
                        ->suffix('%')
                        ->step(0.01)
                        ->minValue(0)
                        ->maxValue(100),
                    Forms\Components\Toggle::make('activo')
                        ->required()
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
                    Tables\Columns\TextColumn::make('porcentaje')
                        ->numeric()
                        ->sortable()
                        ->suffix('%'),
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
                'index' => Pages\ListImpuestos::route('/'),
                'create' => Pages\CreateImpuesto::route('/create'),
                'edit' => Pages\EditImpuesto::route('/{record}/edit'),
            ];
        }
    }
