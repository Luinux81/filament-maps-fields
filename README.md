# Filament Maps Fields

Componentes de Filament para trabajar con mapas interactivos usando Leaflet.js.

Este paquete proporciona campos de formulario y entradas de infolist para seleccionar ubicaciones en mapas interactivos dentro de paneles de administración Filament.

## Características

✨ **Dual Mode** - Soporta dos modos de almacenamiento:
- **Modo JSON** (recomendado): Guarda coordenadas como `{latitude: X, longitude: Y}` en un solo campo
- **Modo Legacy**: Guarda coordenadas en campos separados para compatibilidad con código existente

🗺️ **Mapas Interactivos** - Basado en Leaflet.js con soporte completo para:
- Click para seleccionar ubicación
- Pegar coordenadas desde el portapapeles
- Zoom y navegación
- Modo solo lectura

🎨 **Integración Perfecta con Filament** - Funciona como cualquier otro campo de Filament:
- Validación integrada
- Soporte para notación de punto (dot notation)
- Compatible con formularios y recursos

## Requisitos

- PHP 8.1 o superior
- Laravel 10.x, 11.x, 12.x o 13.x
- Filament 3.x o 4.x
- Livewire 3.x

## Instalación

### 1. Instalar el paquete via Composer

```bash
composer require lbcdev/filament-maps-fields
```

Este paquete instalará automáticamente sus dependencias:

- `lbcdev/livewire-maps-core` - El componente Livewire base
- `lbcdev/map-geometries` - Clases de geometría para mapas

### 2. Incluir Leaflet.js en tu panel de Filament

**Opción recomendada: Usando RenderHook**

Agrega el siguiente código en tu `AdminPanelProvider` (o el provider de tu panel):

```php
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ... otras configuraciones
        ->renderHook(
            'panels::head.end',
            fn(): string => view('filament-maps-fields::hooks.leaflet-assets')->render()
        );
}
```

**Nota:** El hook incluye automáticamente Leaflet.js y Leaflet.draw (requerido para MapBoundsField).

**Opción alternativa: Layout personalizado**

Si estás usando un layout personalizado de Filament, agrega estos scripts en el `<head>` (antes de `@livewireStyles`):

```blade
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
```

### 3. (Opcional) Publicar configuración

Si deseas personalizar la configuración del mapa:

```bash
php artisan vendor:publish --tag=livewire-maps-config
```

## Uso Básico

### Componentes Disponibles

Este paquete incluye dos componentes principales:

- **MapField** - Seleccionar un punto en el mapa (latitud/longitud)
- **MapBoundsField** - Seleccionar un área rectangular (bounds)

Ambos componentes soportan **Dual Mode** (JSON y Legacy).

### MapField en Formularios

El componente `MapField` te permite agregar un mapa interactivo a tus formularios de Filament. Los usuarios pueden hacer clic en el mapa para seleccionar una ubicación.

**MapField soporta dos modos de operación:**

1. **Modo JSON (Recomendado)** - Las coordenadas se guardan como JSON en un solo campo
2. **Modo Legacy** - Las coordenadas se guardan en campos separados de latitud/longitud

#### Comparación Rápida

| Característica | Modo JSON | Modo Legacy |
|----------------|-----------|-------------|
| **Configuración** | `MapField::make('location')` | `MapField::make('map')->latitude('lat')->longitude('lng')` |
| **Campos en BD** | 1 campo JSON | 2 campos decimales |
| **Simplicidad** | ✅ Muy simple | ⚠️ Requiere configuración |
| **Uso recomendado** | Proyectos nuevos | Proyectos existentes con campos separados |
| **Migración** | Fácil desde Legacy | - |

---

### Modo JSON (Recomendado)

En este modo, el campo guarda las coordenadas como un objeto JSON `{latitude: X, longitude: Y}` directamente en el campo especificado.

#### Ventajas del Modo JSON

✅ **Más simple** - No necesitas configurar campos adicionales
✅ **Más limpio** - Un solo campo en la base de datos
✅ **Más intuitivo** - El campo del mapa guarda la ubicación
✅ **Menos propenso a errores** - No hay que sincronizar múltiples campos

#### Estructura de Base de Datos

```php
// Migration
Schema::create('places', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->json('location')->nullable();  // Guarda {latitude: X, longitude: Y}
});
```

#### Modelo

```php
use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
    protected $fillable = ['name', 'location'];

    protected $casts = [
        'location' => 'array',  // Cast automático a array
    ];
}
```

#### Uso en Formulario

```php
use LBCDev\FilamentMapsFields\Forms\Components\MapField;
use Filament\Forms\Components\TextInput;

public static function form(Form $form): Form
{
    return $form->schema([
        TextInput::make('name')
            ->required(),

        MapField::make('location')  // ¡Así de simple!
            ->height(400)
            ->columnSpanFull(),
    ]);
}
```

#### Acceder a las Coordenadas

```php
// Crear
$place = Place::create([
    'name' => 'Madrid',
    'location' => [
        'latitude' => 40.4168,
        'longitude' => -3.7038,
    ],
]);

// Leer
$latitude = $place->location['latitude'];   // 40.4168
$longitude = $place->location['longitude']; // -3.7038

// Actualizar
$place->update([
    'location' => [
        'latitude' => 41.3851,
        'longitude' => 2.1734,
    ],
]);
```

---

### Modo Legacy (Campos Separados)

En este modo, el campo del mapa es "virtual" y actualiza campos separados de latitud y longitud. Este modo existe para mantener compatibilidad con código existente.

#### Cuándo Usar Modo Legacy

- Cuando ya tienes una base de datos con campos `latitude` y `longitude` separados
- Cuando necesitas mantener compatibilidad con código existente
- Cuando otros sistemas esperan campos separados

#### Estructura de Base de Datos

```php
// Migration
Schema::create('locations', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->decimal('latitude', 10, 8)->nullable();
    $table->decimal('longitude', 11, 8)->nullable();
});
```

#### Modelo

```php
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = ['name', 'latitude', 'longitude'];
}
```

#### Uso en Formulario

```php
use LBCDev\FilamentMapsFields\Forms\Components\MapField;
use Filament\Forms\Components\TextInput;

public static function form(Form $form): Form
{
    return $form->schema([
        TextInput::make('name')
            ->required(),

        MapField::make('map')
            ->latitude('latitude')      // Campo donde se guarda la latitud
            ->longitude('longitude')    // Campo donde se guarda la longitud
            ->height(400)
            ->columnSpanFull(),

        // Opcionalmente puedes mostrar los campos
        TextInput::make('latitude')
            ->disabled()
            ->dehydrated(),
        TextInput::make('longitude')
            ->disabled()
            ->dehydrated(),
    ]);
}
```

#### Ejemplo con Campos Anidados (Dot Notation)

El modo Legacy también soporta notación de punto para campos anidados:

```php
MapField::make('location_map')
    ->latitude('location.latitude')      // Notación de punto
    ->longitude('location.longitude')    // Notación de punto
```

---

### Migrar de Modo Legacy a Modo JSON

Si tienes una aplicación existente en Modo Legacy y quieres migrar a Modo JSON:

#### 1. Crear migración para añadir campo JSON

```php
Schema::table('locations', function (Blueprint $table) {
    $table->json('location')->nullable()->after('name');
});
```

#### 2. Migrar datos existentes

```php
use App\Models\Location;

Location::whereNotNull('latitude')
    ->whereNotNull('longitude')
    ->each(function ($location) {
        $location->update([
            'location' => [
                'latitude' => $location->latitude,
                'longitude' => $location->longitude,
            ],
        ]);
    });
```

#### 3. Actualizar el modelo

```php
protected $fillable = ['name', 'location'];

protected $casts = [
    'location' => 'array',
];
```

#### 4. Actualizar el formulario

```php
// Antes (Legacy)
MapField::make('map')
    ->latitude('latitude')
    ->longitude('longitude')

// Después (JSON)
MapField::make('location')
```

#### 5. (Opcional) Eliminar campos antiguos

Una vez verificado que todo funciona:

```php
Schema::table('locations', function (Blueprint $table) {
    $table->dropColumn(['latitude', 'longitude']);
});
```

### Configuración del MapField

El `MapField` acepta varios métodos de configuración:

```php
// Modo JSON (simple)
MapField::make('location')
    ->height(500)                   // Altura del mapa en píxeles (default: 400)
    ->zoom(15)                      // Nivel de zoom inicial (default: 15)
    ->showPasteButton()            // Mostrar botón para pegar coordenadas
    ->showLabel()                   // Mostrar etiqueta con coordenadas (default: true)
    ->interactive(true)             // Permitir interacción (default: true)
    ->readOnly()                    // Hacer el mapa de solo lectura

// Modo Legacy (requiere latitude y longitude)
MapField::make('map')
    ->latitude('latitude')          // Campo de latitud (requerido en modo Legacy)
    ->longitude('longitude')        // Campo de longitud (requerido en modo Legacy)
    ->height(500)
    ->zoom(15)
```

### Métodos Disponibles

#### `latitude(string $field)` - Solo Modo Legacy

Define el campo donde se guardará la latitud. Soporta notación de punto para campos anidados.

**Nota:** Si usas este método, el campo entra en Modo Legacy.

```php
->latitude('latitude')              // Campo simple
->latitude('location.latitude')     // Campo anidado
```

#### `longitude(string $field)` - Solo Modo Legacy

Define el campo donde se guardará la longitud. Soporta notación de punto para campos anidados.

**Nota:** Si usas este método junto con `latitude()`, el campo entra en Modo Legacy.

```php
->longitude('longitude')            // Campo simple
->longitude('location.longitude')   // Campo anidado
```

#### `height(int $pixels)`

Define la altura del mapa en píxeles.

```php
->height(400)    // Altura por defecto
->height(600)    // Mapa más alto
```

#### `zoom(int $level)`

Define el nivel de zoom inicial del mapa (típicamente 1-20).

```php
->zoom(15)    // Zoom por defecto
->zoom(10)    // Zoom más alejado
->zoom(18)    // Zoom más cercano
```

#### `showPasteButton(bool $show = true)`

Muestra u oculta el botón para pegar coordenadas desde el portapapeles.

```php
->showPasteButton()          // Mostrar botón
->showPasteButton(false)     // Ocultar botón
```

#### `showLabel(bool $show = true)`

Muestra u oculta la etiqueta con las coordenadas actuales.

```php
->showLabel()          // Mostrar etiqueta (por defecto)
->showLabel(false)     // Ocultar etiqueta
```

#### `interactive(bool $interactive = true)`

Define si el mapa es interactivo (clickable) o de solo lectura.

```php
->interactive()          // Mapa interactivo (por defecto)
->interactive(false)     // Mapa de solo lectura
```

#### `readOnly(bool $condition = true)`

Alias de `interactive(false)` para mantener consistencia con la API de Filament.

```php
->readOnly()            // Mapa de solo lectura
->readOnly(false)       // Mapa interactivo
```

## Ejemplos Avanzados

### Formulario Completo de Ubicación

```php
use LBCDev\FilamentMapsFields\Forms\Components\MapField;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Section;

public static function form(Form $form): Form
{
    return $form->schema([
        Section::make('Información Básica')
            ->schema([
                TextInput::make('name')
                    ->label('Nombre del lugar')
                    ->required(),
                    
                Textarea::make('description')
                    ->label('Descripción')
                    ->rows(3),
            ]),
            
        Section::make('Ubicación')
            ->description('Haz clic en el mapa para seleccionar la ubicación exacta')
            ->schema([
                MapField::make('location')
                    ->latitude('latitude')
                    ->longitude('longitude')
                    ->height(500)
                    ->zoom(15)
                    ->showPasteButton(),
                    
                TextInput::make('latitude')
                    ->label('Latitud')
                    ->disabled()
                    ->dehydrated()
                    ->numeric()
                    ->required(),
                    
                TextInput::make('longitude')
                    ->label('Longitud')
                    ->disabled()
                    ->dehydrated()
                    ->numeric()
                    ->required(),
            ])
            ->columns(1),
    ]);
}
```

### Mapa de Solo Lectura en Vista

```php
use LBCDev\FilamentMapsFields\Forms\Components\MapField;

public static function form(Form $form): Form
{
    return $form->schema([
        MapField::make('location')
            ->latitude('latitude')
            ->longitude('longitude')
            ->readOnly()           // No permite cambiar la ubicación
            ->showLabel(false)     // Oculta la etiqueta de coordenadas
            ->height(400)
            ->zoom(15),
    ]);
}
```

### MapBoundsField - Seleccionar Área Rectangular

**Modo JSON:**
```php
use LBCDev\FilamentMapsFields\Forms\Components\MapBoundsField;

MapBoundsField::make('bounds')  // Guarda {sw_lat, sw_lng, ne_lat, ne_lng}
    ->height(400)
    ->zoom(10);
```

**Modo Legacy:**
```php
MapBoundsField::make('area_bounds')
    ->southWestLat('sw_lat')
    ->southWestLng('sw_lng')
    ->northEastLat('ne_lat')
    ->northEastLng('ne_lng');
```

### Múltiples Ubicaciones en el Mismo Formulario

```php
use LBCDev\FilamentMapsFields\Forms\Components\MapField;
use Filament\Forms\Components\Section;

public static function form(Form $form): Form
{
    return $form->schema([
        Section::make('Ubicación de Origen')
            ->schema([
                MapField::make('origin_location')
                    ->latitude('origin_latitude')
                    ->longitude('origin_longitude')
                    ->height(300),
            ]),
            
        Section::make('Ubicación de Destino')
            ->schema([
                MapField::make('destination_location')
                    ->latitude('destination_latitude')
                    ->longitude('destination_longitude')
                    ->height(300),
            ]),
    ]);
}
```

## Configuración Global

Puedes personalizar los valores por defecto en `config/livewire-maps.php`:

```php
return [
    // Coordenadas por defecto cuando no se especifican
    'default_latitude' => 40.416775,
    'default_longitude' => -3.703790,
    'default_zoom' => 15,
    'default_height' => 400,

    // Configuración del tile layer
    'tile_layer' => [
        'url' => 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
        'attribution' => '© OpenStreetMap contributors',
        'max_zoom' => 19,
    ],

    // Comportamiento por defecto
    'interactive' => true,
    'show_label' => true,
    'show_paste_button' => false,
];
```

## Modo Debug

Para activar el modo debug y ver logs en la consola del navegador:

1. Agregar `?map_debug=1` a la URL
2. O configurar `APP_DEBUG_MAP=true` en tu archivo `.env`

Esto mostrará información detallada sobre los eventos del mapa y actualizaciones de coordenadas.

## Estructura de Base de Datos Recomendada

### Migración Básica

```php
Schema::create('locations', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->text('description')->nullable();
    $table->decimal('latitude', 10, 8);    // Precisión: 8 decimales
    $table->decimal('longitude', 11, 8);   // Precisión: 8 decimales
    $table->timestamps();
});
```

### Migración con Campos Anidados (JSON)

```php
Schema::create('locations', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->json('location');    // Contiene latitude y longitude
    $table->timestamps();
});
```

Con el modelo correspondiente:

```php
class Location extends Model
{
    protected $fillable = ['name', 'location'];
    
    protected $casts = [
        'location' => 'array',
    ];
}
```

## Compatibilidad

- ✅ Filament 3.x
- ✅ Filament 4.x
- ✅ Livewire 3.x
- ✅ Laravel 10.x, 11.x, 12.x, 13.x
- ✅ PHP 8.1, 8.2, 8.3

## MapEntry en Infolists

MapEntry muestra un mapa de solo lectura en infolists. Soporta Dual Mode igual que MapField.

**Modo JSON:**
```php
use LBCDev\FilamentMapsFields\Infolists\Entries\MapEntry;

MapEntry::make('location')
    ->height(300)
    ->zoom(15)
```

**Modo Legacy:**
```php
MapEntry::make('map')
    ->latitude('latitude')
    ->longitude('longitude')
    ->height(300)
```

## Próximas Características

- [ ] Soporte para múltiples marcadores en un solo campo
- [ ] Integración con servicios de geocodificación
- [ ] Soporte para otras geometrías (polígonos, líneas)

## Soporte

Si encuentras algún problema o tienes sugerencias:

- 🐛 [Reportar un bug](https://github.com/Luinux81/filament-maps-fields/issues)
- 💡 [Solicitar una característica](https://github.com/Luinux81/filament-maps-fields/issues)

## Licencia

Este paquete es software de código abierto licenciado bajo la [Licencia MIT](LICENSE).

## Créditos

- Desarrollado por [LBCDev](https://github.com/Luinux81)
- Construido sobre [lbcdev/livewire-maps-core](https://github.com/Luinux81/livewire-maps-core)
- Utiliza [Leaflet.js](https://leafletjs.com/) para los mapas
- Construido para [Filament](https://filamentphp.com/)
