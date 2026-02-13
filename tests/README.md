# Tests - Filament Maps Fields

Este directorio contiene la suite de tests para el paquete `lbcdev/filament-maps-fields`.

## Estructura de Tests

```shell
tests/
├── Fixtures/           # Datos de prueba (modelos, migraciones)
│   ├── Models/        # Modelos Eloquent para testing
│   └── database/      # Migraciones de prueba
├── Unit/              # Tests unitarios de componentes
└── TestCase.php       # Clase base para todos los tests
```

## Concepto de Tests

### Fixtures

Los **fixtures** son datos y estructuras de prueba que simulan un entorno real de Laravel/Filament:

- **Modelos** (`Fixtures/Models/`): Modelos Eloquent que representan diferentes casos de uso:
  - `Location.php`: Modelo tradicional con campos separados `latitude` y `longitude`
  - `Store.php`: Modelo con campo JSON `ubicacion` para coordenadas anidadas
  - `Area.php`: Modelo para límites geográficos (bounds) con campos separados y JSON

- **Migraciones** (`Fixtures/database/migrations/`): Crean las tablas necesarias en la base de datos de prueba (SQLite en memoria)

### Tests Unitarios

Los tests están organizados por componente y funcionalidad:

#### MapField

- **MapFieldTest.php**: Tests básicos del componente MapField (configuración, getters, setters)
- **MapFieldBackwardCompatibilityTest.php**: Compatibilidad con modo tradicional (campos separados)
- **MapFieldJsonNotationTest.php**: Soporte para notación de puntos en campos JSON anidados
- **MapFieldRequiredValidationTest.php**: Validación de campos requeridos

#### MapEntry

- **MapEntryTest.php**: Tests del componente MapEntry para infolists

#### MapBoundsField (pendiente de actualización)

- **MapBoundsFieldTest.php**: Tests básicos del componente MapBoundsField
- **MapBoundsFieldJsonNotationTest.php**: Soporte para notación de puntos en bounds
- **MapBoundsFieldRequiredValidationTest.php**: Validación de bounds requeridos

#### MapBoundsEntry (pendiente de actualización)

- **MapBoundsEntryTest.php**: Tests del componente MapBoundsEntry para infolists

## Cómo Ejecutar los Tests

### Requisitos Previos

Asegúrate de tener las dependencias instaladas:

```bash
cd packages/fields
composer install
```

### Ejecutar Todos los Tests

```bash
cd packages/fields
vendor/bin/phpunit
```

### Ejecutar Tests Específicos

**Por archivo:**

```bash
vendor/bin/phpunit tests/Unit/MapFieldTest.php
```

**Por suite:**

```bash
vendor/bin/phpunit --testsuite Unit
```

**Por filtro de nombre:**

```bash
vendor/bin/phpunit --filter MapField
```

**Tests específicos de MapField:**

```bash
vendor/bin/phpunit tests/Unit/MapFieldTest.php \
                   tests/Unit/MapFieldBackwardCompatibilityTest.php \
                   tests/Unit/MapFieldJsonNotationTest.php \
                   tests/Unit/MapFieldRequiredValidationTest.php
```

**Tests específicos de MapBoundsField:**

```bash
vendor/bin/phpunit tests/Unit/MapBoundsFieldTest.php \
                   tests/Unit/MapBoundsFieldJsonNotationTest.php \
                   tests/Unit/MapBoundsFieldRequiredValidationTest.php
```

### Ejecutar con Cobertura

```bash
vendor/bin/phpunit --coverage-html coverage
```

## Entorno de Testing

Los tests utilizan **Orchestra Testbench**, que proporciona un entorno Laravel completo para testing de paquetes:

- **Base de datos**: SQLite en memoria (`:memory:`)
- **Service Providers**: Se cargan automáticamente Filament, Livewire y FilamentMapsFieldsServiceProvider
- **Migraciones**: Se ejecutan automáticamente antes de cada test

## Convenciones

- Cada test debe tener el atributo `/** @test */` o el prefijo `test_` en el nombre del método
- Los nombres de tests usan snake_case y describen claramente qué se está probando
- Los fixtures se crean/destruyen automáticamente en cada test (base de datos en memoria)
- Se usa `$this->assertEquals()`, `$this->assertTrue()`, etc. para aserciones

## Estado Actual

✅ **Actualizados a namespace `LBCDev\FilamentMapsFields`:**

- MapField (todos los tests)
- MapEntry
- TestCase base
- Modelos de fixtures

⏳ **Pendientes de actualización:**

- MapBoundsField (todos los tests)
- MapBoundsEntry

## Testing Manual con Webapp

Para pruebas manuales e integración completa, puedes crear una aplicación Laravel de prueba:

### 1. Crear Webapp Base

```bash
cd /workspace/packages/fields
bash /workspace/.devcontainer/php/scripts/create-webapp.sh
# Responder 'n' cuando pregunte si instalar el paquete
```

### 2. Instalar Filament

```bash
cd webapp
composer require filament/filament:"^3.0"
php artisan filament:install --panels
php artisan make:filament-user # Crear usuario Admin, con email: admin@example.com y password: password1234567890 
```

### 3. Instalar el Paquete

Necesitas agregar el repositorio del paquete core al  composer.json de la webapp:

```bash
cd /workspace/packages/fields/webapp
composer config repositories.core '{"type": "path", "url": "../../core", "options": {"symlink": true}}'
composer config repositories.geometries '{"type": "path", "url": "../../geometries", "options": {"symlink": true}}'
composer require lbcdev/filament-maps-fields:@dev
```

### 4. Crear Recurso de Prueba

```bash
# Crear modelo y migración
php artisan make:model Location -m

# Crear recurso Filament
php artisan make:filament-resource Location
```

### 5. Configurar Modelo y Migración

**Migration** (`database/migrations/xxxx_create_locations_table.php`):

```php
$table->string('name');
$table->decimal('latitude', 10, 8)->nullable();
$table->decimal('longitude', 11, 8)->nullable();
```

**Model** (`app/Models/Location.php`):

```php
protected $fillable = ['name', 'latitude', 'longitude'];
protected $casts = ['latitude' => 'float', 'longitude' => 'float'];
```

### 6. Incluir Leaflet.js en el Panel

**AdminPanelProvider** (`app/Providers/Filament/AdminPanelProvider.php`):

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

### 7. Configurar Recurso Filament

**Resource** (`app/Filament/Resources/LocationResource.php`):

```php
use LBCDev\FilamentMapsFields\Forms\Components\MapField;

public static function form(Form $form): Form
{
    return $form->schema([
        Forms\Components\TextInput::make('name')->required(),
        MapField::make('map')
            ->latitude('latitude')
            ->longitude('longitude')
            ->height(400)
            ->columnSpanFull(),
    ]);
}
```

### 8. Ejecutar y Probar

```bash
php artisan migrate
php artisan make:filament-user  # Crear usuario admin
php artisan serve
```

Acceder a `http://localhost:8000/admin` y probar el recurso Location.

## Notas

- Los tests de MapField cubren tanto el modo tradicional (campos separados) como el modo JSON (campos anidados)
- La retrocompatibilidad es importante: los tests aseguran que ambos modos funcionen correctamente
- Los fixtures permiten probar casos reales sin necesidad de una base de datos externa
