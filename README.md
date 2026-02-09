# Filament Map Field

[![Filament v3](https://img.shields.io/badge/Filament-v3-orange?style=flat-square)](https://filamentphp.com)
[![Filament v4](https://img.shields.io/badge/Filament-v4-orange?style=flat-square)](https://filamentphp.com)
[![PHP Version](https://img.shields.io/badge/PHP-8.1%2B-blue?style=flat-square)](https://php.net)
[![Laravel](https://img.shields.io/badge/Laravel-10%20%7C%2011%20%7C%2012-red?style=flat-square)](https://laravel.com)
[![License](https://img.shields.io/badge/license-MIT-green?style=flat-square)](LICENSE)

Un paquete de Filament que proporciona componentes de campo de mapa para formularios e infolists, utilizando el componente Livewire [lbcdev-map](https://github.com/Luinux81/livewire-lbcdev-component-map).

> **✨ Compatible con Filament v3 y v4** - Actualiza sin preocupaciones, sin cambios en tu código.

## ✨ Características

- 🗺️ **MapField** para formularios Filament (selección de puntos interactiva)
- 📋 **MapEntry** para infolists Filament (visualización de puntos)
- 📐 **MapBoundsField** para formularios Filament (selección de áreas rectangulares)
- 📊 **MapBoundsEntry** para infolists Filament (visualización de áreas)
- 🎯 Integración perfecta con el componente Livewire lbcdev-map
- 📍 Soporte para campos de latitud/longitud separados
- 🔄 **Soporte para campos JSON anidados** (v1.1.0+) - Usa notación de punto: `'ubicacion.latitud'`
- ⚡ Actualización reactiva de coordenadas
- 🎨 Compatible con el tema de Filament
- 🔧 Altamente configurable
- ✨ **Compatible con Filament v3 y v4** - Sin cambios necesarios al actualizar

## 📋 Requisitos

- PHP 8.1+ (PHP 8.2+ recomendado para Filament v4)
- Laravel 10.x, 11.x o 12.x
- **Filament 3.x o 4.x** ✨
- Livewire 3.x
- [lbcdev/livewire-map-component](https://github.com/Luinux81/livewire-lbcdev-component-map) ^1.0

> **Nota:** Este paquete es compatible con **Filament v3 y v4**. No necesitas hacer cambios en tu código al actualizar de Filament v3 a v4.

## 📦 Instalación

### 1. Instalar el paquete via Composer

```bash
composer require lbcdev/filament-map-field
```

### 2. Incluir Leaflet.js en tu layout

El paquete depende de `lbcdev/livewire-map-component`, que requiere Leaflet.js. Agrega estos scripts en el `<head>` de tu layout principal:

```html
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<!-- Leaflet Draw (solo si usas MapBoundsField) -->
<link rel="stylesheet" href="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.css" />
<script src="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.js"></script>
```

> **Nota:** Leaflet Draw solo es necesario si vas a usar `MapBoundsField` para seleccionar áreas rectangulares. Si solo usas `MapField` para puntos, no es necesario incluirlo.

Con Filament v4 puedes usar un hook para incluir los tags de Leaflet. Agrega el siguiente código a tu archivo `app/Providers/Filament/AdminPanelProvider.php`:

```php
    public function panel(Panel $panel): Panel{
        return $panel
            ...
            ->renderHook(
                'panels::head.end',
                fn(): string => view('filament.hooks.leaflet-assets')->render()
            )
            ...
    }
```

### 3. (Opcional) Publicar las vistas

Si deseas personalizar las vistas del componente:

```bash
php artisan vendor:publish --tag=filament-map-field-views
```

Las vistas se publicarán en `resources/views/vendor/filament-map-field/`.

## 🚀 Uso

### MapField en Formularios

El componente `MapField` permite a los usuarios seleccionar coordenadas de forma interactiva en un formulario.

#### Uso básico

```php
use Lbcdev\FilamentMapField\Forms\Components\MapField;

MapField::make('location')
    ->latitude('latitude')
    ->longitude('longitude');
```

#### Con todas las opciones

```php
MapField::make('location')
    ->latitude('latitude')      // Campo donde se guardará la latitud
    ->longitude('longitude')    // Campo donde se guardará la longitud
    ->height(500)              // Altura del mapa en píxeles (default: 400)
    ->zoom(15)                 // Nivel de zoom inicial (default: 15)
    ->showPasteButton()        // Mostrar botón para pegar coordenadas
    ->showLabel()              // Mostrar etiqueta con coordenadas
    ->interactive();           // Permitir interacción (default: true)
```

#### Modo de solo lectura

```php
// Usando readOnly() - Compatible con la API estándar de Filament
MapField::make('location')
    ->latitude('latitude')
    ->longitude('longitude')
    ->readOnly();

// O usando interactive(false) - Mismo resultado
MapField::make('location')
    ->latitude('latitude')
    ->longitude('longitude')
    ->interactive(false);
```

#### Ejemplo completo en un Resource

```php
<?php

namespace App\Filament\Resources;

use App\Models\Location;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Lbcdev\FilamentMapField\Forms\Components\MapField;

class LocationResource extends Resource
{
    protected static ?string $model = Location::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('address')
                    ->maxLength(255),

                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('latitude')
                            ->numeric()
                            ->required(),

                        Forms\Components\TextInput::make('longitude')
                            ->numeric()
                            ->required(),
                    ]),

                MapField::make('map')
                    ->latitude('latitude')
                    ->longitude('longitude')
                    ->height(500)
                    ->zoom(15)
                    ->showPasteButton()
                    ->columnSpanFull(),
            ]);
    }
}
```

#### ⚠️ Importante: Uso con Notación de Punto (JSON)

Cuando uses notación de punto para campos JSON anidados, el **primer parámetro de `make()`** debe coincidir con el campo padre:

```php
// ✅ CORRECTO: make() usa el campo padre 'ubicacion'
MapField::make('ubicacion')
    ->latitude('ubicacion.latitud')
    ->longitude('ubicacion.longitud')
    ->columnSpanFull();

// ❌ INCORRECTO: make() usa 'map' pero los campos son 'ubicacion.latitud'
// Esto causará error "The ubicación field is required" en modo create
MapField::make('map')
    ->latitude('ubicacion.latitud')
    ->longitude('ubicacion.longitud')
    ->columnSpanFull();
```

**Modelo con campo JSON:**

```php
class Store extends Model
{
    protected $fillable = ['name', 'ubicacion'];

    protected $casts = [
        'ubicacion' => 'array', // Campo JSON
    ];
}
```

**Migración:**

```php
Schema::create('stores', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->json('ubicacion')->nullable(); // Campo JSON
    $table->timestamps();
});
```

### MapEntry en Infolists

El componente `MapEntry` muestra las coordenadas en un mapa de solo lectura en infolists.

#### Uso básico de MapEntry

```php
use Lbcdev\FilamentMapField\Infolists\Entries\MapEntry;

MapEntry::make('location')
    ->latitude('latitude')
    ->longitude('longitude');
```

#### Con opciones

```php
MapEntry::make('location')
    ->latitude('latitude')
    ->longitude('longitude')
    ->height(400)
    ->zoom(15)
    ->showLabel();
```

#### Ejemplo completo de un Resource

```php
<?php

namespace App\Filament\Resources;

use App\Models\Location;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Lbcdev\FilamentMapField\Infolists\Entries\MapEntry;

class LocationResource extends Resource
{
    protected static ?string $model = Location::class;

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\TextEntry::make('name'),

                Infolists\Components\TextEntry::make('address'),

                Infolists\Components\Grid::make(2)
                    ->schema([
                        Infolists\Components\TextEntry::make('latitude')
                            ->numeric(decimalPlaces: 6),

                        Infolists\Components\TextEntry::make('longitude')
                            ->numeric(decimalPlaces: 6),
                    ]),

                MapEntry::make('map')
                    ->latitude('latitude')
                    ->longitude('longitude')
                    ->height(400)
                    ->zoom(15)
                    ->columnSpanFull(),
            ]);
    }
}
```

### MapBoundsField en Formularios

El componente `MapBoundsField` permite a los usuarios seleccionar áreas rectangulares de forma interactiva en un formulario.

#### Uso básico

```php
use Lbcdev\FilamentMapField\Forms\Components\MapBoundsField;

MapBoundsField::make('area')
    ->southWestLat('sw_lat')
    ->southWestLng('sw_lng')
    ->northEastLat('ne_lat')
    ->northEastLng('ne_lng');
```

#### Con todas las opciones

```php
MapBoundsField::make('area')
    ->southWestLat('sw_lat')        // Campo para latitud suroeste
    ->southWestLng('sw_lng')        // Campo para longitud suroeste
    ->northEastLat('ne_lat')        // Campo para latitud noreste
    ->northEastLng('ne_lng')        // Campo para longitud noreste
    ->height(500)                   // Altura del mapa en píxeles
    ->zoom(13)                      // Nivel de zoom inicial
    ->showLabel()                   // Mostrar etiqueta con coordenadas
    ->defaultCenter(40.4168, -3.7038); // Centro por defecto (Madrid)
```

#### Con campos JSON anidados

```php
// ✅ CORRECTO: make() usa el campo padre 'bounds'
MapBoundsField::make('bounds')
    ->southWestLat('bounds.sw_lat')
    ->southWestLng('bounds.sw_lng')
    ->northEastLat('bounds.ne_lat')
    ->northEastLng('bounds.ne_lng')
    ->height(500)
    ->zoom(13);
```

> **Nota:** Al igual que con `MapField`, cuando uses notación de punto, el primer parámetro de `make()` debe coincidir con el campo padre JSON. Ver la sección "⚠️ Importante: Uso con Notación de Punto" arriba para más detalles.

### MapBoundsEntry en Infolists

El componente `MapBoundsEntry` muestra áreas rectangulares en un mapa de solo lectura en infolists.

#### Uso básico

```php
use Lbcdev\FilamentMapField\Infolists\Entries\MapBoundsEntry;

MapBoundsEntry::make('area')
    ->southWestLat('sw_lat')
    ->southWestLng('sw_lng')
    ->northEastLat('ne_lat')
    ->northEastLng('ne_lng');
```

#### Con opciones

```php
MapBoundsEntry::make('area')
    ->southWestLat('sw_lat')
    ->southWestLng('sw_lng')
    ->northEastLat('ne_lat')
    ->northEastLng('ne_lng')
    ->height(400)
    ->zoom(13)
    ->showLabel();
```

## 🎨 Métodos Disponibles

### MapField (Forms)

| Método | Descripción | Default |
| ------ | ----------- | ------- |
| `latitude(string $field)` | Campo donde se guardará la latitud. Soporta notación de punto para JSON: `'ubicacion.latitud'` | `null` |
| `longitude(string $field)` | Campo donde se guardará la longitud. Soporta notación de punto para JSON: `'ubicacion.longitud'` | `null` |
| `height(int $height)` | Altura del mapa en píxeles | `400` |
| `zoom(int $zoom)` | Nivel de zoom inicial (1-20) | `15` |
| `showPasteButton(bool $show = true)` | Mostrar botón para pegar coordenadas | `false` |
| `showLabel(bool $show = true)` | Mostrar etiqueta con coordenadas | `true` |
| `interactive(bool $interactive = true)` | Permitir interacción con el mapa | `true` |
| `readOnly(bool $condition = true)` | Hacer el mapa de solo lectura (alias de `interactive(false)`) | `false` |

### MapEntry (Infolists)

| Método | Descripción | Default |
| ------ | ----------- | ------- |
| `latitude(string $field)` | Campo de donde leer la latitud. Soporta notación de punto para JSON: `'ubicacion.latitud'` | `null` |
| `longitude(string $field)` | Campo de donde leer la longitud. Soporta notación de punto para JSON: `'ubicacion.longitud'` | `null` |
| `height(int $height)` | Altura del mapa en píxeles | `300` |
| `zoom(int $zoom)` | Nivel de zoom inicial (1-20) | `15` |
| `showLabel(bool $show = true)` | Mostrar etiqueta con coordenadas | `true` |

### MapBoundsField (Forms)

| Método | Descripción | Default |
| ------ | ----------- | ------- |
| `southWestLat(string $field)` | Campo para latitud suroeste. Soporta notación de punto: `'bounds.sw_lat'` | `null` |
| `southWestLng(string $field)` | Campo para longitud suroeste. Soporta notación de punto: `'bounds.sw_lng'` | `null` |
| `northEastLat(string $field)` | Campo para latitud noreste. Soporta notación de punto: `'bounds.ne_lat'` | `null` |
| `northEastLng(string $field)` | Campo para longitud noreste. Soporta notación de punto: `'bounds.ne_lng'` | `null` |
| `height(int $height)` | Altura del mapa en píxeles | `400` |
| `zoom(int $zoom)` | Nivel de zoom inicial (1-20) | `13` |
| `showLabel(bool $show = true)` | Mostrar etiqueta con coordenadas de los límites | `true` |
| `defaultCenter(float $lat, float $lng)` | Centro por defecto del mapa | `[36.9990019, -6.5478919]` |

### MapBoundsEntry (Infolists)

| Método | Descripción | Default |
| ------ | ----------- | ------- |
| `southWestLat(string $field)` | Campo de donde leer latitud suroeste. Soporta notación de punto: `'bounds.sw_lat'` | `null` |
| `southWestLng(string $field)` | Campo de donde leer longitud suroeste. Soporta notación de punto: `'bounds.sw_lng'` | `null` |
| `northEastLat(string $field)` | Campo de donde leer latitud noreste. Soporta notación de punto: `'bounds.ne_lat'` | `null` |
| `northEastLng(string $field)` | Campo de donde leer longitud noreste. Soporta notación de punto: `'bounds.ne_lng'` | `null` |
| `height(int $height)` | Altura del mapa en píxeles | `300` |
| `zoom(int $zoom)` | Nivel de zoom inicial (1-20) | `13` |
| `showLabel(bool $show = true)` | Mostrar etiqueta con coordenadas de los límites | `true` |

## 💡 Ejemplos Avanzados

### Formulario con validación

Los componentes `MapField` y `MapBoundsField` soportan el método `->required()` de forma nativa. Cuando se marca un campo como requerido, automáticamente valida que todos los campos anidados (latitud, longitud, límites) tengan valores.

```php
// Ejemplo 1: MapField con validación requerida
MapField::make('ubicacion')
    ->latitude('ubicacion.latitud')
    ->longitude('ubicacion.longitud')
    ->height(500)
    ->zoom(15)
    ->showPasteButton()
    ->required() // ✅ Valida que latitud y longitud tengan valores
    ->label('Ubicación'),

// Ejemplo 2: MapBoundsField con validación requerida
MapBoundsField::make('limites')
    ->southWestLat('limites.latitud_min')
    ->southWestLng('limites.longitud_min')
    ->northEastLat('limites.latitud_max')
    ->northEastLng('limites.longitud_max')
    ->height(500)
    ->zoom(13)
    ->required() // ✅ Valida que todos los límites tengan valores
    ->label('Límites del área'),

// Ejemplo 3: Validación con campos separados (modo tradicional)
Forms\Components\Grid::make(2)
    ->schema([
        Forms\Components\TextInput::make('latitude')
            ->numeric()
            ->required()
            ->minValue(-90)
            ->maxValue(90)
            ->step(0.000001),

        Forms\Components\TextInput::make('longitude')
            ->numeric()
            ->required()
            ->minValue(-180)
            ->maxValue(180)
            ->step(0.000001),
    ]),

MapField::make('map')
    ->latitude('latitude')
    ->longitude('longitude')
    ->height(600)
    ->zoom(12)
    ->showPasteButton()
    ->columnSpanFull(),
```

> **Nota:** El método `->required()` funciona tanto en modo Create como Edit. La validación se aplica automáticamente a los campos anidados configurados con notación de punto.

### Múltiples mapas en un formulario

```php
Forms\Components\Tabs::make('Locations')
    ->tabs([
        Forms\Components\Tabs\Tab::make('Origen')
            ->schema([
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('origin_latitude')
                            ->numeric()
                            ->required(),
                        Forms\Components\TextInput::make('origin_longitude')
                            ->numeric()
                            ->required(),
                    ]),

                MapField::make('origin_map')
                    ->latitude('origin_latitude')
                    ->longitude('origin_longitude')
                    ->height(400)
                    ->showPasteButton(),
            ]),

        Forms\Components\Tabs\Tab::make('Destino')
            ->schema([
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('destination_latitude')
                            ->numeric()
                            ->required(),
                        Forms\Components\TextInput::make('destination_longitude')
                            ->numeric()
                            ->required(),
                    ]),

                MapField::make('destination_map')
                    ->latitude('destination_latitude')
                    ->longitude('destination_longitude')
                    ->height(400)
                    ->showPasteButton(),
            ]),
    ]),
```

### Campos JSON anidados (v1.1.0+)

El paquete soporta guardar coordenadas en campos JSON anidados usando notación de punto. Esto es útil cuando quieres almacenar las coordenadas en una estructura JSON en lugar de campos separados.

#### Modo 1: Campos separados (tradicional)

```php
// Migración
Schema::create('locations', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->decimal('latitude', 10, 8)->nullable();
    $table->decimal('longitude', 11, 8)->nullable();
});

// Formulario
MapField::make('map')
    ->latitude('latitude')
    ->longitude('longitude');

// Resultado en BD:
// latitude: 40.416775
// longitude: -3.703790
```

#### Modo 2: Campo JSON anidado (nuevo)

```php
// Migración
Schema::create('locations', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->json('ubicacion')->nullable();
});

// Modelo
class Location extends Model
{
    protected $casts = [
        'ubicacion' => 'array',
    ];
}

// Formulario
MapField::make('ubicacion')
    ->latitude('ubicacion.latitud')
    ->longitude('ubicacion.longitud')
    ->height(500)
    ->zoom(15)
    ->showPasteButton();

// Resultado en BD (campo JSON):
// ubicacion: {"latitud": "40.416775", "longitud": "-3.703790"}
```

#### Ventajas del modo JSON

- ✅ Agrupa coordenadas relacionadas en un solo campo
- ✅ Facilita la gestión de múltiples ubicaciones
- ✅ Permite nombres de campos personalizados (latitud/longitud, lat/lng, etc.)
- ✅ 100% retrocompatible con el modo tradicional

#### Ejemplo completo con JSON

```php
<?php

namespace App\Filament\Resources;

use App\Models\Store;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Lbcdev\FilamentMapField\Forms\Components\MapField;

class StoreResource extends Resource
{
    protected static ?string $model = Store::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('address')
                    ->maxLength(255),

                MapField::make('ubicacion')
                    ->label('Ubicación en el mapa')
                    ->latitude('ubicacion.latitud')
                    ->longitude('ubicacion.longitud')
                    ->height(500)
                    ->zoom(15)
                    ->showPasteButton()
                    ->columnSpanFull(),
            ]);
    }
}
```

## 🔧 Personalización

### Publicar y personalizar vistas

```bash
php artisan vendor:publish --tag=filament-map-field-views
```

Las vistas estarán disponibles en:

- `resources/views/vendor/filament-map-field/forms/components/map-field.blade.php`
- `resources/views/vendor/filament-map-field/infolists/entries/map-entry.blade.php`

## 🔄 Compatibilidad con Filament v3 y v4

Este paquete es **totalmente compatible** con Filament v3 y v4 sin necesidad de cambios en tu código.

### ¿Qué significa esto?

- ✅ Puedes usar este paquete con Filament v3
- ✅ Puedes usar este paquete con Filament v4
- ✅ Al actualizar de Filament v3 a v4, **no necesitas cambiar nada** en el código que usa este paquete
- ✅ El paquete detecta automáticamente la versión de Filament y se adapta

### Requisitos según la versión de Filament

#### Para Filament v3

- PHP 8.1+
- Laravel 10.x o 11.x
- Tailwind CSS 3.x (si usas tema personalizado)

#### Para Filament v4

- PHP 8.2+
- Laravel 11.28+ o 12.x
- Tailwind CSS 4.x (si usas tema personalizado)

### Actualización de Filament v3 a v4

Si estás actualizando tu proyecto de Filament v3 a v4:

1. **Actualiza Filament** siguiendo la [guía oficial de actualización](https://filamentphp.com/docs/4.x/support/upgrade-guide)
2. **Actualiza las dependencias**:

   ```bash
   composer update
   ```

3. **¡Listo!** El paquete `filament-map-field` seguirá funcionando sin cambios

No necesitas:

- ❌ Cambiar el código de tus Resources
- ❌ Modificar las llamadas a `MapField` o `MapEntry`
- ❌ Actualizar la sintaxis del paquete

### Nota sobre Tailwind CSS

Si usas un **tema personalizado** en Filament, necesitarás actualizar Tailwind CSS de v3 a v4 al migrar a Filament v4. Esto es un requisito de Filament, no de este paquete específicamente.

Consulta la [guía de actualización de Tailwind CSS v4](https://tailwindcss.com/docs/upgrade-guide) para más detalles.

## 🔄 Actualización del Paquete

### Actualizar a una versión específica

Para actualizar el paquete a una versión específica usando tags de GitHub:

```bash
# Actualizar a la última versión
composer update lbcdev/filament-map-field

# O instalar una versión específica por tag
composer require lbcdev/filament-map-field:1.0.0
```

### Usar una versión específica en composer.json

Puedes especificar la versión exacta en tu `composer.json`:

```json
{
    "require": {
        "lbcdev/filament-map-field": "^1.0"
    }
}
```

O usar un tag específico:

```json
{
    "require": {
        "lbcdev/filament-map-field": "1.0.0"
    }
}
```

### Verificar la versión instalada

```bash
composer show lbcdev/filament-map-field
```

### Limpiar caché después de actualizar

Después de actualizar, es recomendable limpiar las cachés:

```bash
php artisan filament:cache-components
php artisan view:clear
php artisan cache:clear
```

## 🐛 Solución de Problemas

Si encuentras problemas al usar el paquete, consulta la [Guía de Solución de Problemas](TROUBLESHOOTING.md) que incluye:

- ✅ El mapa no actualiza los campos del formulario
- ✅ El mapa no se muestra
- ✅ Problemas de estilos
- ✅ Errores comunes y sus soluciones

## 🤝 Créditos

Este paquete utiliza:

- [lbcdev/livewire-map-component](https://github.com/Luinux81/livewire-lbcdev-component-map) - Componente Livewire de mapas
- [Leaflet.js](https://leafletjs.com/) - Biblioteca de mapas interactivos
- [Filament](https://filamentphp.com/) - Framework de administración para Laravel

## 📄 Licencia

Este paquete es software de código abierto licenciado bajo la [Licencia MIT](LICENSE).

## 🐛 Soporte

Si encuentras algún problema o tienes sugerencias:

- 🐛 [Reportar un bug](https://github.com/Luinux81/filament-lbcdev-map-field/issues)
- 💡 [Solicitar una característica](https://github.com/Luinux81/filament-lbcdev-map-field/issues)

## 👨‍💻 Autor

Desarrollado por [Luinux81](https://github.com/Luinux81)
