<?php

        namespace App\Filament\Resources;

        use App\Filament\Resources\ClienteResource\Pages;
        use App\Models\Cliente;
        use App\Models\Departamento;
        use App\Models\Municipio;
        use App\Models\TipoDocumento;
        use Filament\Forms;
        use Filament\Forms\Form;
        use Filament\Forms\Get;
        use Filament\Forms\Set;
        use Filament\Resources\Resource;
        use Filament\Tables;
        use Filament\Tables\Table;
        use Illuminate\Database\Eloquent\Builder;

        class ClienteResource extends Resource
        {
            protected static ?string $model = Cliente::class;

            protected static ?string $navigationIcon = 'heroicon-o-user-group';

            protected static ?string $navigationGroup = 'Restaurante';

            protected static ?int $navigationSort = 5;

            public static function form(Form $form): Form
            {
                return $form
                    ->schema([
                        Forms\Components\Select::make('tipo_cliente')
                            ->label('Tipo de Cliente')
                            ->options([
                                'natural' => 'Persona Natural',
                                'juridico' => 'Persona Jurídica',
                            ])
                            ->default('natural')
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn (Set $set) => $set('digito_verificacion', null)),

                        Forms\Components\Select::make('tipo_documento_id')
                            ->label('Tipo de Documento')
                            ->options(function (Get $get) {
                                if ($get('tipo_cliente') === 'natural') {
                                    return TipoDocumento::whereNotIn('codigo', ['NIT'])
                                        ->pluck('nombre', 'id');
                                }

                                return TipoDocumento::pluck('nombre', 'id');
                            })
                            ->relationship(name: 'tipoDocumento', titleAttribute: 'nombre')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live(),

                        Forms\Components\TextInput::make('numero_documento')
                            ->label(function (Get $get) {
                                $tipoDocumento = TipoDocumento::find($get('tipo_documento_id'));
                                if ($tipoDocumento && $tipoDocumento->codigo === '6') {
                                    return 'Número de documento sin dígito de verificación';
                                }
                                return 'Número de documento';
                            })
                            ->required()
                            ->maxLength(20),

                        Forms\Components\TextInput::make('digito_verificacion')
                            ->label('Dígito de verificación')
                            ->visible(function (Get $get) {
                                $tipoDocumento = TipoDocumento::find($get('tipo_documento_id'));
                                return $tipoDocumento && $tipoDocumento->codigo === '6';
                            })
                            ->required(function (Get $get) {
                                $tipoDocumento = TipoDocumento::find($get('tipo_documento_id'));
                                return $tipoDocumento && $tipoDocumento->codigo === '6';
                            })
                            ->maxLength(1),

                        Forms\Components\TextInput::make('nombres')
                            ->label('Nombres')
                            ->required(fn (Get $get) => $get('tipo_cliente') === 'natural')
                            ->visible(fn (Get $get) => $get('tipo_cliente') === 'natural')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('apellidos')
                            ->label('Apellidos')
                            ->required(fn (Get $get) => $get('tipo_cliente') === 'natural')
                            ->visible(fn (Get $get) => $get('tipo_cliente') === 'natural')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('razon_social')
                            ->label('Razón Social')
                            ->required(fn (Get $get) => $get('tipo_cliente') === 'juridico')
                            ->visible(fn (Get $get) => $get('tipo_cliente') === 'juridico')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('telefono')
                            ->label('Teléfono')
                            ->tel()
                            ->maxLength(20),

                        Forms\Components\TextInput::make('email')
                            ->label('Correo Electrónico')
                            ->email()
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('departamento_id')
                            ->label('Departamento')
                            ->options(Departamento::pluck('nombre', 'id'))
                            ->relationship(name: 'departamento', titleAttribute: 'nombre')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn (Set $set) => $set('municipio_id', null)),

                        Forms\Components\Select::make('municipio_id')
                            ->label('Municipio')
                            ->options(function (Get $get) {
                                $departamentoId = $get('departamento_id');
                                if ($departamentoId) {
                                    return Municipio::where('departamento_id', $departamentoId)
                                        ->pluck('nombre', 'id');
                                }
                                return [];
                            })
                            ->relationship(name: 'municipio', titleAttribute: 'nombre')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->disabled(fn (Get $get) => !$get('departamento_id')),

                        Forms\Components\TextInput::make('direccion')
                            ->label('Dirección')
                            ->maxLength(255),

                        Forms\Components\Toggle::make('tiene_credito')
                            ->label('¿Tiene crédito?')
                            ->default(false)
                            ->live(),

                        Forms\Components\TextInput::make('cupo_credito')
                            ->label('Cupo de crédito')
                            ->numeric()
                            ->prefix('$')
                            ->visible(fn (Get $get) => $get('tiene_credito'))
                            ->required(fn (Get $get) => $get('tiene_credito'))
                            ->default(0),

                        Forms\Components\Toggle::make('activo')
                            ->label('Activo')
                            ->default(true),
                    ]);
            }

            public static function table(Table $table): Table
            {
                return $table
                    ->columns([
                        Tables\Columns\TextColumn::make('tipo_cliente')
                            ->label('Tipo')
                            ->badge()
                            ->formatStateUsing(fn (string $state) => $state === 'natural' ? 'Natural' : 'Jurídico')
                            ->colors([
                                'primary' => 'natural',
                                'success' => 'juridico',
                            ]),
                        Tables\Columns\TextColumn::make('tipoDocumento.nombre')
                            ->label('Tipo Doc.')
                            ->sortable(),
                        Tables\Columns\TextColumn::make('numero_documento')
                            ->label('Número Doc.')
                            ->searchable()
                            ->formatStateUsing(function ($state, $record) {
                                if ($record->digito_verificacion) {
                                    return "$state-$record->digito_verificacion";
                                }
                                return $state;
                            }),
                        Tables\Columns\TextColumn::make('nombre_completo')
                            ->label('Nombre')
                            ->searchable(query: function (Builder $query, string $search) {
                                return $query->where('nombres', 'like', "%{$search}%")
                                    ->orWhere('apellidos', 'like', "%{$search}%")
                                    ->orWhere('razon_social', 'like', "%{$search}%");
                            }),
                        Tables\Columns\TextColumn::make('telefono')
                            ->label('Teléfono')
                            ->searchable(),
                        Tables\Columns\TextColumn::make('email')
                            ->label('Email')
                            ->toggleable(),
                        Tables\Columns\TextColumn::make('departamento.nombre')
                            ->label('Departamento')
                            ->toggleable(isToggledHiddenByDefault: true),
                        Tables\Columns\TextColumn::make('municipio.nombre')
                            ->label('Municipio')
                            ->toggleable(isToggledHiddenByDefault: true),
                        Tables\Columns\IconColumn::make('tiene_credito')
                            ->label('Crédito')
                            ->boolean(),
                        Tables\Columns\TextColumn::make('cupo_credito')
                            ->label('Cupo')
                            ->money('COP')
                            ->toggleable(),
                        Tables\Columns\IconColumn::make('activo')
                            ->label('Activo')
                            ->boolean(),
                    ])
                    ->filters([
                        Tables\Filters\SelectFilter::make('tipo_cliente')
                            ->label('Tipo Cliente')
                            ->options([
                                'natural' => 'Persona Natural',
                                'juridico' => 'Persona Jurídica',
                            ]),
                        Tables\Filters\SelectFilter::make('tipo_documento_id')
                            ->label('Tipo Documento')
                            ->relationship('tipoDocumento', 'nombre'),
                        Tables\Filters\SelectFilter::make('departamento_id')
                            ->label('Departamento')
                            ->relationship('departamento', 'nombre')
                            ->searchable()
                            ->preload(),
                        Tables\Filters\TernaryFilter::make('tiene_credito')
                            ->label('Con Crédito'),
                        Tables\Filters\TernaryFilter::make('activo')
                            ->label('Activo'),
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
                    'index' => Pages\ListClientes::route('/'),
                    'create' => Pages\CreateCliente::route('/create'),
                    'edit' => Pages\EditCliente::route('/{record}/edit'),
                ];
            }
        }
