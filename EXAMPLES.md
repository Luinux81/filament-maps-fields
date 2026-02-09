# Ejemplos de Uso

Este documento contiene ejemplos prácticos de cómo usar el paquete `filament-map-field`.

## Ejemplo 1: Resource Completo con Ubicaciones

```php
<?php

namespace App\Filament\Resources;

use App\Models\Location;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Lbcdev\FilamentMapField\Forms\Components\MapField;
use Lbcdev\FilamentMapField\Infolists\Entries\MapEntry;

class LocationResource extends Resource
{
    protected static ?string $model = Location::class;
    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información General')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\Textarea::make('description')
                            ->label('Descripción')
                            ->rows(3)
                            ->columnSpanFull(),
                        
                        Forms\Components\TextInput::make('address')
                            ->label('Dirección')
                            ->maxLength(500)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Coordenadas')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('latitude')
                                    ->label('Latitud')
                                    ->numeric()
                                    ->required()
                                    ->minValue(-90)
                                    ->maxValue(90)
                                    ->step(0.000001)
                                    ->placeholder('40.416775'),
                                
                                Forms\Components\TextInput::make('longitude')
                                    ->label('Longitud')
                                    ->numeric()
                                    ->required()
                                    ->minValue(-180)
                                    ->maxValue(180)
                                    ->step(0.000001)
                                    ->placeholder('-3.703790'),
                            ]),
                        
                        MapField::make('map')
                            ->label('Seleccionar en el mapa')
                            ->latitude('latitude')
                            ->longitude('longitude')
                            ->height(500)
                            ->zoom(15)
                            ->showPasteButton()
                            ->showLabel()
                            ->columnSpanFull()
                            ->helperText('Haz clic en el mapa para seleccionar la ubicación o arrastra el marcador.'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('address')
                    ->label('Dirección')
                    ->searchable()
                    ->limit(50),
                
                Tables\Columns\TextColumn::make('latitude')
                    ->label('Latitud')
                    ->numeric(decimalPlaces: 6)
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('longitude')
                    ->label('Longitud')
                    ->numeric(decimalPlaces: 6)
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Información General')
                    ->schema([
                        Infolists\Components\TextEntry::make('name')
                            ->label('Nombre'),
                        
                        Infolists\Components\TextEntry::make('description')
                            ->label('Descripción')
                            ->columnSpanFull(),
                        
                        Infolists\Components\TextEntry::make('address')
                            ->label('Dirección')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                
                Infolists\Components\Section::make('Ubicación')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('latitude')
                                    ->label('Latitud')
                                    ->numeric(decimalPlaces: 6),
                                
                                Infolists\Components\TextEntry::make('longitude')
                                    ->label('Longitud')
                                    ->numeric(decimalPlaces: 6),
                            ]),
                        
                        MapEntry::make('map')
                            ->label('Mapa')
                            ->latitude('latitude')
                            ->longitude('longitude')
                            ->height(400)
                            ->zoom(15)
                            ->showLabel()
                            ->columnSpanFull(),
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
            'index' => Pages\ListLocations::route('/'),
            'create' => Pages\CreateLocation::route('/create'),
            'view' => Pages\ViewLocation::route('/{record}'),
            'edit' => Pages\EditLocation::route('/{record}/edit'),
        ];
    }
}
```

## Ejemplo 2: Modelo Location

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'address',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
    ];
}
```

## Ejemplo 3: Migración

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('address', 500)->nullable();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
```

## Ejemplo 4: Formulario con Múltiples Ubicaciones

```php
Forms\Components\Repeater::make('locations')
    ->label('Ubicaciones')
    ->schema([
        Forms\Components\TextInput::make('name')
            ->label('Nombre')
            ->required(),
        
        Forms\Components\Grid::make(2)
            ->schema([
                Forms\Components\TextInput::make('latitude')
                    ->label('Latitud')
                    ->numeric()
                    ->required(),
                
                Forms\Components\TextInput::make('longitude')
                    ->label('Longitud')
                    ->numeric()
                    ->required(),
            ]),
        
        MapField::make('map')
            ->latitude('latitude')
            ->longitude('longitude')
            ->height(300)
            ->showPasteButton()
            ->columnSpanFull(),
    ])
    ->columnSpanFull()
    ->collapsible()
    ->itemLabel(fn (array $state): ?string => $state['name'] ?? null),
```

## Ejemplo 5: Formulario con Tabs para Origen y Destino

```php
Forms\Components\Tabs::make('Route')
    ->tabs([
        Forms\Components\Tabs\Tab::make('Origen')
            ->icon('heroicon-o-map-pin')
            ->schema([
                Forms\Components\TextInput::make('origin_name')
                    ->label('Nombre del origen')
                    ->required(),
                
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('origin_latitude')
                            ->label('Latitud')
                            ->numeric()
                            ->required(),
                        
                        Forms\Components\TextInput::make('origin_longitude')
                            ->label('Longitud')
                            ->numeric()
                            ->required(),
                    ]),
                
                MapField::make('origin_map')
                    ->label('Ubicación de origen')
                    ->latitude('origin_latitude')
                    ->longitude('origin_longitude')
                    ->height(400)
                    ->zoom(15)
                    ->showPasteButton(),
            ]),
        
        Forms\Components\Tabs\Tab::make('Destino')
            ->icon('heroicon-o-flag')
            ->schema([
                Forms\Components\TextInput::make('destination_name')
                    ->label('Nombre del destino')
                    ->required(),
                
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('destination_latitude')
                            ->label('Latitud')
                            ->numeric()
                            ->required(),
                        
                        Forms\Components\TextInput::make('destination_longitude')
                            ->label('Longitud')
                            ->numeric()
                            ->required(),
                    ]),
                
                MapField::make('destination_map')
                    ->label('Ubicación de destino')
                    ->latitude('destination_latitude')
                    ->longitude('destination_longitude')
                    ->height(400)
                    ->zoom(15)
                    ->showPasteButton(),
            ]),
    ])
    ->columnSpanFull(),
```

## Ejemplo 6: Infolist con Secciones Colapsables

```php
Infolists\Components\Section::make('Ubicación en el Mapa')
    ->description('Visualización de la ubicación en el mapa')
    ->schema([
        Infolists\Components\Grid::make(3)
            ->schema([
                Infolists\Components\TextEntry::make('latitude')
                    ->label('Latitud')
                    ->numeric(decimalPlaces: 6)
                    ->copyable()
                    ->icon('heroicon-o-map-pin'),

                Infolists\Components\TextEntry::make('longitude')
                    ->label('Longitud')
                    ->numeric(decimalPlaces: 6)
                    ->copyable()
                    ->icon('heroicon-o-map-pin'),

                Infolists\Components\TextEntry::make('coordinates')
                    ->label('Coordenadas')
                    ->state(fn ($record) => "{$record->latitude}, {$record->longitude}")
                    ->copyable()
                    ->icon('heroicon-o-clipboard'),
            ]),

        MapEntry::make('map')
            ->label(false)
            ->latitude('latitude')
            ->longitude('longitude')
            ->height(500)
            ->zoom(15)
            ->columnSpanFull(),
    ])
    ->collapsible()
    ->collapsed(false),
```

## Ejemplo 7: Campos JSON Anidados (v1.1.0+)

Este ejemplo muestra cómo usar campos JSON anidados para almacenar coordenadas en una estructura JSON en lugar de campos separados.

### Migración

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('address', 500)->nullable();
            $table->json('ubicacion')->nullable(); // Campo JSON para coordenadas
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
```

### Modelo

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'address',
        'ubicacion',
    ];

    protected $casts = [
        'ubicacion' => 'array', // Cast a array para manejar JSON
    ];

    // Accessor para obtener la latitud
    public function getLatitudAttribute(): ?float
    {
        return $this->ubicacion['latitud'] ?? null;
    }

    // Accessor para obtener la longitud
    public function getLongitudAttribute(): ?float
    {
        return $this->ubicacion['longitud'] ?? null;
    }
}
```

### Resource con MapField

```php
<?php

namespace App\Filament\Resources;

use App\Models\Store;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Lbcdev\FilamentMapField\Forms\Components\MapField;
use Lbcdev\FilamentMapField\Infolists\Entries\MapEntry;

class StoreResource extends Resource
{
    protected static ?string $model = Store::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información de la Tienda')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Textarea::make('description')
                            ->label('Descripción')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('address')
                            ->label('Dirección')
                            ->maxLength(500)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Ubicación')
                    ->description('Selecciona la ubicación de la tienda en el mapa')
                    ->schema([
                        // Usando notación de punto para campos JSON anidados
                        MapField::make('ubicacion')
                            ->label('Ubicación en el mapa')
                            ->latitude('ubicacion.latitud')
                            ->longitude('ubicacion.longitud')
                            ->height(500)
                            ->zoom(15)
                            ->showPasteButton()
                            ->showLabel()
                            ->columnSpanFull()
                            ->helperText('Las coordenadas se guardarán en formato JSON: {"latitud": "...", "longitud": "..."}'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('address')
                    ->label('Dirección')
                    ->searchable()
                    ->limit(50),

                Tables\Columns\TextColumn::make('latitud')
                    ->label('Latitud')
                    ->numeric(decimalPlaces: 6)
                    ->sortable(),

                Tables\Columns\TextColumn::make('longitud')
                    ->label('Longitud')
                    ->numeric(decimalPlaces: 6)
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Información de la Tienda')
                    ->schema([
                        Infolists\Components\TextEntry::make('name')
                            ->label('Nombre'),

                        Infolists\Components\TextEntry::make('description')
                            ->label('Descripción')
                            ->columnSpanFull(),

                        Infolists\Components\TextEntry::make('address')
                            ->label('Dirección')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Ubicación')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('latitud')
                                    ->label('Latitud')
                                    ->numeric(decimalPlaces: 6)
                                    ->copyable(),

                                Infolists\Components\TextEntry::make('longitud')
                                    ->label('Longitud')
                                    ->numeric(decimalPlaces: 6)
                                    ->copyable(),
                            ]),

                        // Usando notación de punto para leer campos JSON anidados
                        MapEntry::make('ubicacion')
                            ->label('Mapa')
                            ->latitude('ubicacion.latitud')
                            ->longitude('ubicacion.longitud')
                            ->height(400)
                            ->zoom(15)
                            ->showLabel()
                            ->columnSpanFull(),
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
            'index' => Pages\ListStores::route('/'),
            'create' => Pages\CreateStore::route('/create'),
            'view' => Pages\ViewStore::route('/{record}'),
            'edit' => Pages\EditStore::route('/{record}/edit'),
        ];
    }
}
```

### Resultado en la Base de Datos

Cuando guardas un registro con el formulario anterior, el campo `ubicacion` se almacena como JSON:

```json
{
    "latitud": "40.416775",
    "longitud": "-3.703790"
}
```

### Ventajas de este Enfoque

1. **Agrupación lógica**: Las coordenadas están agrupadas en un solo campo JSON
2. **Flexibilidad**: Puedes agregar más datos relacionados con la ubicación en el mismo campo JSON
3. **Nombres personalizados**: Usa los nombres que prefieras (latitud/longitud, lat/lng, etc.)
4. **Compatibilidad**: Funciona perfectamente con el modo tradicional de campos separados

---

## Ejemplo 8: Selección de Áreas con MapBoundsField

Este ejemplo muestra cómo usar `MapBoundsField` para seleccionar áreas rectangulares en un mapa, ideal para definir zonas de cobertura, áreas de servicio, o regiones geográficas.

### Caso de Uso

Imagina que tienes una aplicación de delivery y necesitas definir las zonas de cobertura de cada restaurante. Cada zona se define por un rectángulo en el mapa.

### 1. Migración

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_zones', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();

            // Opción 1: Campos separados (modo tradicional)
            $table->decimal('sw_lat', 10, 8)->nullable()->comment('Southwest Latitude');
            $table->decimal('sw_lng', 11, 8)->nullable()->comment('Southwest Longitude');
            $table->decimal('ne_lat', 10, 8)->nullable()->comment('Northeast Latitude');
            $table->decimal('ne_lng', 11, 8)->nullable()->comment('Northeast Longitude');

            // Opción 2: Campo JSON (modo anidado)
            // $table->json('bounds')->nullable();

            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_zones');
    }
};
```

### 2. Modelo

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryZone extends Model
{
    protected $fillable = [
        'name',
        'description',
        'sw_lat',
        'sw_lng',
        'ne_lat',
        'ne_lng',
        // 'bounds', // Si usas modo JSON
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        // 'bounds' => 'array', // Si usas modo JSON
    ];

    /**
     * Verifica si un punto está dentro de la zona
     */
    public function containsPoint(float $lat, float $lng): bool
    {
        return $lat >= $this->sw_lat
            && $lat <= $this->ne_lat
            && $lng >= $this->sw_lng
            && $lng <= $this->ne_lng;
    }

    /**
     * Calcula el área aproximada en km²
     */
    public function getAreaAttribute(): float
    {
        $latDiff = abs($this->ne_lat - $this->sw_lat);
        $lngDiff = abs($this->ne_lng - $this->sw_lng);

        // Aproximación simple (111 km por grado)
        return $latDiff * $lngDiff * 111 * 111;
    }
}
```

### 3. Resource con MapBoundsField (Modo Tradicional)

```php
<?php

namespace App\Filament\Resources;

use App\Models\DeliveryZone;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Lbcdev\FilamentMapField\Forms\Components\MapBoundsField;
use Lbcdev\FilamentMapField\Infolists\Entries\MapBoundsEntry;

class DeliveryZoneResource extends Resource
{
    protected static ?string $model = DeliveryZone::class;
    protected static ?string $navigationIcon = 'heroicon-o-map';
    protected static ?string $navigationLabel = 'Zonas de Entrega';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información de la Zona')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre de la Zona')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Ej: Centro de Madrid'),

                        Forms\Components\Textarea::make('description')
                            ->label('Descripción')
                            ->rows(3)
                            ->placeholder('Describe el área de cobertura...')
                            ->columnSpanFull(),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Zona Activa')
                            ->default(true)
                            ->helperText('Desactiva la zona si temporalmente no hay servicio'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Definir Área en el Mapa')
                    ->schema([
                        MapBoundsField::make('coverage_area')
                            ->label('Área de Cobertura')
                            ->southWestLat('sw_lat')
                            ->southWestLng('sw_lng')
                            ->northEastLat('ne_lat')
                            ->northEastLng('ne_lng')
                            ->height(500)
                            ->zoom(13)
                            ->defaultCenter(40.4168, -3.7038) // Madrid
                            ->showLabel()
                            ->helperText('Arrastra los vértices del rectángulo para definir el área de cobertura')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('description')
                    ->label('Descripción')
                    ->limit(50)
                    ->searchable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activa')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creada')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Estado')
                    ->placeholder('Todas')
                    ->trueLabel('Solo activas')
                    ->falseLabel('Solo inactivas'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Información de la Zona')
                    ->schema([
                        Infolists\Components\TextEntry::make('name')
                            ->label('Nombre'),

                        Infolists\Components\TextEntry::make('description')
                            ->label('Descripción')
                            ->columnSpanFull(),

                        Infolists\Components\IconEntry::make('is_active')
                            ->label('Estado')
                            ->boolean()
                            ->trueIcon('heroicon-o-check-circle')
                            ->falseIcon('heroicon-o-x-circle')
                            ->trueColor('success')
                            ->falseColor('danger'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Área de Cobertura')
                    ->schema([
                        MapBoundsEntry::make('coverage_area')
                            ->label('Mapa de la Zona')
                            ->southWestLat('sw_lat')
                            ->southWestLng('sw_lng')
                            ->northEastLat('ne_lat')
                            ->northEastLng('ne_lng')
                            ->height(400)
                            ->zoom(13)
                            ->showLabel()
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
```

### 4. Alternativa: Modo JSON Anidado

Si prefieres usar un campo JSON para almacenar los límites:

```php
// En la migración, usa:
$table->json('bounds')->nullable();

// En el modelo, agrega:
protected $casts = [
    'bounds' => 'array',
];

// En el formulario, usa:
MapBoundsField::make('coverage_area')
    ->label('Área de Cobertura')
    ->southWestLat('bounds.sw_lat')
    ->southWestLng('bounds.sw_lng')
    ->northEastLat('bounds.ne_lat')
    ->northEastLng('bounds.ne_lng')
    ->height(500)
    ->zoom(13)
    ->defaultCenter(40.4168, -3.7038)
    ->columnSpanFull();

// En el infolist, usa:
MapBoundsEntry::make('coverage_area')
    ->label('Mapa de la Zona')
    ->southWestLat('bounds.sw_lat')
    ->southWestLng('bounds.sw_lng')
    ->northEastLat('bounds.ne_lat')
    ->northEastLng('bounds.ne_lng')
    ->height(400)
    ->zoom(13)
    ->columnSpanFull();
```

### 5. Uso Práctico: Verificar si un Punto está en la Zona

```php
// En un controlador o servicio
$deliveryAddress = [
    'lat' => 40.4200,
    'lng' => -3.7050,
];

$availableZones = DeliveryZone::where('is_active', true)
    ->get()
    ->filter(function ($zone) use ($deliveryAddress) {
        return $zone->containsPoint(
            $deliveryAddress['lat'],
            $deliveryAddress['lng']
        );
    });

if ($availableZones->isEmpty()) {
    return response()->json([
        'message' => 'Lo sentimos, no tenemos cobertura en tu área',
        'available' => false,
    ]);
}

return response()->json([
    'message' => 'Tenemos cobertura en tu área',
    'available' => true,
    'zones' => $availableZones->pluck('name'),
]);
```

### 6. Widget para Mostrar Zonas Activas

```php
<?php

namespace App\Filament\Widgets;

use App\Models\DeliveryZone;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DeliveryZoneStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalZones = DeliveryZone::count();
        $activeZones = DeliveryZone::where('is_active', true)->count();
        $totalArea = DeliveryZone::where('is_active', true)
            ->get()
            ->sum(fn($zone) => $zone->area);

        return [
            Stat::make('Zonas Totales', $totalZones)
                ->description('Total de zonas configuradas')
                ->descriptionIcon('heroicon-m-map')
                ->color('primary'),

            Stat::make('Zonas Activas', $activeZones)
                ->description('Zonas con servicio activo')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Área Total', number_format($totalArea, 2) . ' km²')
                ->description('Área total de cobertura')
                ->descriptionIcon('heroicon-m-square-3-stack-3d')
                ->color('info'),
        ];
    }
}
```

### Ventajas de MapBoundsField

1. **Interfaz Visual**: Los usuarios pueden definir áreas arrastrando el rectángulo en el mapa
2. **Precisión**: Las coordenadas se actualizan automáticamente al editar el rectángulo
3. **Flexibilidad**: Soporta tanto campos separados como campos JSON anidados
4. **Validación Visual**: Los usuarios ven inmediatamente el área que están definiendo
5. **Integración Perfecta**: Funciona igual que MapField pero para áreas rectangulares

### Casos de Uso Adicionales

- **Zonas de Cobertura**: Delivery, servicios a domicilio
- **Áreas de Servicio**: Empresas de limpieza, mantenimiento
- **Regiones Geográficas**: Administración territorial, estudios demográficos
- **Zonas de Precio**: Tarifas diferentes según la ubicación
- **Áreas de Restricción**: Zonas donde no se presta servicio
- **Territorios de Ventas**: Asignación de vendedores por área geográfica

---

## Ejemplo 8: Validación con `required()`

Los componentes `MapField` y `MapBoundsField` soportan validación nativa usando el método `->required()`. Cuando se marca un campo como requerido, automáticamente valida que todos los campos anidados tengan valores.

### Caso de Uso: Recurso Localidad con Validación

```php
<?php

namespace App\Filament\Resources;

use App\Models\Localidad;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Lbcdev\FilamentMapField\Forms\Components\MapField;
use Lbcdev\FilamentMapField\Forms\Components\MapBoundsField;

class LocalidadResource extends Resource
{
    protected static ?string $model = Localidad::class;
    protected static ?string $navigationIcon = 'heroicon-s-globe-europe-africa';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->required()
                    ->maxLength(255)
                    ->label('Nombre de la Localidad'),

                Forms\Components\Section::make('Ubicación')
                    ->description('Define el punto central y los límites de la localidad')
                    ->schema([
                        // MapField con validación requerida
                        MapField::make('ubicacion')
                            ->latitude('ubicacion.latitud')
                            ->longitude('ubicacion.longitud')
                            ->height(500)
                            ->zoom(15)
                            ->showPasteButton()
                            ->required() // ✅ Valida que latitud y longitud tengan valores
                            ->columnSpan(1)
                            ->label('Punto Central'),

                        // MapBoundsField con validación requerida
                        MapBoundsField::make('limites')
                            ->southWestLat('limites.latitud_min')
                            ->southWestLng('limites.longitud_min')
                            ->northEastLat('limites.latitud_max')
                            ->northEastLng('limites.longitud_max')
                            ->height(500)
                            ->zoom(13)
                            ->required() // ✅ Valida que todos los límites tengan valores
                            ->columnSpan('full')
                            ->label('Límites del Área'),
                    ])
                    ->columns(2),
            ]);
    }
}
```

### Modelo Localidad

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Localidad extends Model
{
    protected $fillable = [
        'nombre',
        'ubicacion',
        'limites',
    ];

    protected $casts = [
        'ubicacion' => 'array',
        'limites' => 'array',
    ];
}
```

### Migración

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('localidades', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->json('ubicacion'); // { "latitud": 40.4168, "longitud": -3.7038 }
            $table->json('limites');   // { "latitud_min": 40.4, "longitud_min": -3.8, ... }
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('localidades');
    }
};
```

### Comportamiento de la Validación

#### ✅ En modo Create (Crear)
- Si el usuario intenta guardar sin completar los campos del mapa, verá el mensaje: **"El campo de ubicación es requerido"** o **"El campo de límites es requerido"**
- La validación se ejecuta automáticamente al intentar guardar el formulario
- Los campos anidados (`ubicacion.latitud`, `ubicacion.longitud`, etc.) se validan correctamente

#### ✅ En modo Edit (Editar)
- Si el usuario borra los valores del mapa, la validación también se aplica
- Funciona igual que en modo Create

### Ventajas de usar `->required()`

1. **Validación Automática**: No necesitas crear reglas de validación personalizadas
2. **Funciona en Create y Edit**: La validación se aplica en ambos modos sin configuración adicional
3. **Mensajes Claros**: Los usuarios reciben mensajes de error descriptivos
4. **Compatible con Notación de Punto**: Funciona perfectamente con campos JSON anidados
5. **Sin Conflictos**: No interfiere con otras validaciones del formulario

### Personalizar Mensajes de Error

Si quieres personalizar los mensajes de error, puedes usar el método `validationMessages()`:

```php
MapField::make('ubicacion')
    ->latitude('ubicacion.latitud')
    ->longitude('ubicacion.longitud')
    ->required()
    ->validationMessages([
        'required' => 'Por favor, selecciona una ubicación en el mapa.',
    ]),

MapBoundsField::make('limites')
    ->southWestLat('limites.latitud_min')
    ->southWestLng('limites.longitud_min')
    ->northEastLat('limites.latitud_max')
    ->northEastLng('limites.longitud_max')
    ->required()
    ->validationMessages([
        'required' => 'Por favor, define los límites del área en el mapa.',
    ]),
```

### Validación Condicional

También puedes usar validación condicional con `requiredIf()`:

```php
MapField::make('ubicacion')
    ->latitude('ubicacion.latitud')
    ->longitude('ubicacion.longitud')
    ->requiredIf('tipo_negocio', 'fisico') // Solo requerido si es negocio físico
    ->label('Ubicación del Negocio'),
```
