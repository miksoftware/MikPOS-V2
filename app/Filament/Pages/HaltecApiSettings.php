<?php

namespace App\Filament\Pages;

use App\Settings\HaltecApiSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class HaltecApiSetting extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-key';
    protected static string $settings = HaltecApiSettings::class;

    public function getTitle(): string
    {
        return __('Configuración API Haltec');
    }

    protected static ?string $navigationGroup = 'Configuración';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Entorno Activo')
                    ->description('Selecciona el entorno que deseas utilizar')
                    ->schema([
                        Forms\Components\Select::make('haltec_environment')
                            ->label('Entorno')
                            ->options([
                                'production' => 'Producción',
                                'test' => 'Pruebas'
                            ])
                            ->default('test')
                            ->required(),
                    ]),

                Forms\Components\Section::make('Credenciales API Producción')
                    ->description('Ingresa los datos proporcionados por Haltec para el entorno de producción.')
                    ->schema([
                        Forms\Components\TextInput::make('haltec_api_url_production')
                            ->label('URL de API (Producción)')
                            ->required()
                            ->placeholder('https://api.haltec.com/production')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('haltec_user')
                            ->label('Usuario API')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('haltec_password')
                            ->label('Contraseña API')
                            ->password()
                            ->required()
                            ->maxLength(255)
                            ->revealable(),

                        Forms\Components\TextInput::make('haltec_client_id')
                            ->label('Client ID')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('haltec_client_secret')
                            ->label('Client Secret')
                            ->password()
                            ->required()
                            ->maxLength(255)
                            ->revealable(),
                    ])->columns(2),

                Forms\Components\Section::make('Credenciales API Pruebas')
                    ->description('Ingresa los datos proporcionados por Haltec para el entorno de pruebas.')
                    ->schema([
                        Forms\Components\TextInput::make('haltec_api_url_test')
                            ->label('URL de API (Pruebas)')
                            ->required()
                            ->placeholder('https://api.haltec.com/test')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('haltec_user_test')
                            ->label('Usuario API (Pruebas)')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('haltec_password_test')
                            ->label('Contraseña API (Pruebas)')
                            ->password()
                            ->required()
                            ->maxLength(255)
                            ->revealable(),

                        Forms\Components\TextInput::make('haltec_client_id_test')
                            ->label('Client ID (Pruebas)')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('haltec_client_secret_test')
                            ->label('Client Secret (Pruebas)')
                            ->password()
                            ->required()
                            ->maxLength(255)
                            ->revealable(),
                    ])->columns(2),
            ]);
    }
}
