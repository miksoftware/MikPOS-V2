<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentMethodResource\Pages;
use App\Models\PaymentMethod;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PaymentMethodResource extends Resource
{
    protected static ?string $model = PaymentMethod::class;
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationGroup = 'Configuración';
    protected static ?string $navigationLabel = 'Medios de Pago';

    protected static ?string $modelLabel = 'Medio de Pago';
    protected static ?string $pluralModelLabel = 'Medios de Pago';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Textarea::make('description')
                    ->label('Descripción')
                    ->maxLength(255),

                Forms\Components\Select::make('dian_payment_method_id')
                    ->label('Código DIAN')
                    ->relationship('dianPaymentMethod', 'description')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->getSearchResultsUsing(fn (string $search) =>
                        \App\Models\DianPaymentMethod::where('description', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%")
                            ->get()
                            ->mapWithKeys(fn ($method) => [$method->id => "{$method->code} - {$method->description}"])
                    )
                    ->getOptionLabelUsing(fn ($value): ?string =>
                        \App\Models\DianPaymentMethod::find($value)?->code . ' - ' .
                        \App\Models\DianPaymentMethod::find($value)?->description
                    ),

                Forms\Components\Toggle::make('is_active')
                    ->label('Activo')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),

                Tables\Columns\TextColumn::make('dianPaymentMethod.code')
                    ->label('Código DIAN')
                    ->searchable(),

                Tables\Columns\TextColumn::make('dianPaymentMethod.description')
                    ->label('Descripción DIAN')
                    ->searchable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('dian_payment_method_id')
                    ->label('Código DIAN')
                    ->relationship('dianPaymentMethod', 'description')
                    ->searchable()
                    ->preload(),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Activo')
                    ->placeholder('Todos')
                    ->trueLabel('Activos')
                    ->falseLabel('Inactivos'),
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
            'index' => Pages\ListPaymentMethods::route('/'),
            'create' => Pages\CreatePaymentMethod::route('/create'),
            'edit' => Pages\EditPaymentMethod::route('/{record}/edit'),
        ];
    }
}
