<?php

    namespace App\Filament\Resources;

    use App\Filament\Resources\DianPaymentMethodResource\Pages;
    use App\Models\DianPaymentMethod;
    use Filament\Forms;
    use Filament\Forms\Form;
    use Filament\Resources\Resource;
    use Filament\Tables;
    use Filament\Tables\Table;

    class DianPaymentMethodResource extends Resource
    {
        protected static ?string $model = DianPaymentMethod::class;
        protected static ?string $navigationIcon = 'heroicon-o-document-text';
        protected static ?string $navigationGroup = 'Configuración';
        protected static ?string $navigationLabel = 'Códigos DIAN';

        protected static ?string $modelLabel = 'DIAN Medio de Pago';
        protected static ?string $pluralModelLabel = 'DIAN Medios de Pago';

        public static function table(Table $table): Table
        {
            return $table
                ->columns([
                    Tables\Columns\TextColumn::make('code')
                        ->label('Código')
                        ->searchable(),
                    Tables\Columns\TextColumn::make('description')
                        ->label('Descripción')
                        ->searchable(),
                ])
                ->defaultSort('code');
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
                'index' => Pages\ListDianPaymentMethods::route('/'),
            ];
        }

        public static function canCreate(): bool
        {
            return false; // No permitir crear nuevos códigos DIAN
        }
    }
