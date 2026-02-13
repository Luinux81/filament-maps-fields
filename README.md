# Filament Maps Fields

Componentes de Filament para trabajar con mapas interactivos usando Leaflet.js.

Este paquete proporciona campos de formulario y entradas de infolist para seleccionar ubicaciones en mapas interactivos dentro de paneles de administración Filament.

## Requisitos

- PHP 8.1 o superior
- Laravel 10.x, 11.x o 12.x
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

### 2. Incluir Leaflet.js en tu layout

Agrega estos scripts en el `<head>` de tu layout de Filament (antes de `@livewireStyles`):

```blade
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
```

Si estás usando un layout personalizado de Filament, agrega esto en tu archivo de layout.

### 3. (Opcional) Publicar configuración

Si deseas personalizar la configuración del mapa:

```bash
php artisan vendor:publish --tag=livewire-maps-config
```

## Uso Básico

### MapField en Formularios

El componente `MapField` te permite agregar un mapa interactivo a tus formularios de Filament. Los usuarios pueden hacer clic en el mapa para seleccionar una ubicación, y las coordenadas se guardarán automáticamente en los campos que especifiques.

#### Ejemplo Simple

```php
use LBCDev\FilamentMapsFields\Forms\Components\MapField;
use Filament\Forms\Components\TextInput;

public static function form(Form $form): Form
{
    return $form->schema([
        TextInput::make('name')
            ->required(),
            
        MapField::make('location')
            ->latitude('latitude')      // Campo donde se guarda la latitud
            ->longitude('longitude'),   // Campo donde se guarda la longitud
            
        // Los campos de coordenadas pueden estar ocultos
        TextInput::make('latitude')
            ->hidden(),
        TextInput::make('longitude')
            ->hidden(),
    ]);
}
```

#### Ejemplo con Campos Anidados (Dot Notation)

```php
use LBCDev\FilamentMapsFields\Forms\Components\MapField;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\TextInput;

public static function form(Form $form): Form
{
    return $form->schema([
        TextInput::make('name')
            ->required(),
            
        // Los campos pueden estar anidados
        Group::make([
            MapField::make('location_map')
                ->latitude('location.latitude')      // Notación de punto
                ->longitude('location.longitude'),   // Notación de punto
                
            TextInput::make('location.latitude')
                ->label('Latitud')
                ->disabled()
                ->dehydrated(),
                
            TextInput::make('location.longitude')
                ->label('Longitud')
                ->disabled()
                ->dehydrated(),
        ]),
    ]);
}
```

### Configuración del MapField

El `MapField` acepta varios métodos de configuración:

```php
MapField::make('location')
    ->latitude('latitude')          // Campo de latitud (requerido)
    ->longitude('longitude')        // Campo de longitud (requerido)
    ->height(500)                   // Altura del mapa en píxeles (default: 400)
    ->zoom(15)                      // Nivel de zoom inicial (default: 15)
    ->showPasteButton()            // Mostrar botón para pegar coordenadas
    ->showLabel()                   // Mostrar etiqueta con coordenadas (default: true)
    ->interactive(true)             // Permitir interacción (default: true)
    ->readOnly()                    // Hacer el mapa de solo lectura
```

### Métodos Disponibles

#### `latitude(string $field)`

Define el campo donde se guardará la latitud. Soporta notación de punto para campos anidados.

```php
->latitude('latitude')              // Campo simple
->latitude('location.latitude')     // Campo anidado
```

#### `longitude(string $field)`

Define el campo donde se guardará la longitud. Soporta notación de punto para campos anidados.

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
- ✅ Laravel 10.x, 11.x, 12.x
- ✅ PHP 8.1, 8.2, 8.3

## Próximas Características

- [ ] MapEntry para Infolists
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
