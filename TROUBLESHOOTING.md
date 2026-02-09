# Solución de Problemas

Este documento contiene soluciones a problemas comunes al usar `filament-map-field`.

## 🐛 El mapa no actualiza los campos del formulario

### Síntoma
El mapa se muestra correctamente, puedes hacer clic y mover el marcador, la etiqueta de coordenadas se actualiza, pero al guardar el formulario los valores no se persisten.

### Causa
El componente `lbcdev-map` no está emitiendo correctamente los eventos de actualización de coordenadas.

### Solución

#### 1. Verifica que el componente `lbcdev-map` esté actualizado

Asegúrate de tener la última versión del componente:

```bash
composer show lbcdev/livewire-map-component
```

#### 2. Verifica que el componente emita eventos

El componente `lbcdev-map` debe emitir uno de estos eventos cuando las coordenadas cambian:

- **Evento de navegador**: `map-coordinates-updated` (usando `window.dispatchEvent`)
- **Evento de Livewire**: `map-coordinates-updated` (usando `$dispatch` o `$emit`)

El evento debe incluir los datos:
```javascript
{
    latitude: 40.416775,
    longitude: -3.703790
}
```

#### 3. Prueba manualmente los eventos

Abre la consola del navegador (F12) y ejecuta este código:

```javascript
// Prueba el evento de navegador
window.addEventListener('map-coordinates-updated', (event) => {
    console.log('✅ Evento de navegador recibido:', event.detail);
});

// Prueba el evento de Livewire
Livewire.on('map-coordinates-updated', (data) => {
    console.log('✅ Evento de Livewire recibido:', data);
});

console.log('🔍 Listeners registrados. Haz clic en el mapa para probar...');

// Luego haz clic en el mapa y verifica si se imprime alguno de los eventos
```

Si no ves ningún mensaje en la consola al hacer clic en el mapa, significa que el componente `lbcdev-map` no está emitiendo los eventos correctamente.

#### 4. Verifica los nombres de los campos

Asegúrate de que los nombres de los campos en `latitude()` y `longitude()` coincidan exactamente con los nombres en tu modelo:

```php
// ✅ Correcto
MapField::make('map')
    ->latitude('latitude')    // Debe coincidir con el campo en la BD
    ->longitude('longitude')  // Debe coincidir con el campo en la BD

// ❌ Incorrecto
MapField::make('map')
    ->latitude('lat')         // Si el campo en la BD es 'latitude'
    ->longitude('lng')        // Si el campo en la BD es 'longitude'
```

#### 5. Verifica la estructura del formulario

Si los campos están dentro de un `Repeater`, `Tabs`, o cualquier otro contenedor, asegúrate de que los nombres de los campos sean correctos:

```php
// Para campos simples
MapField::make('map')
    ->latitude('latitude')
    ->longitude('longitude')

// Para campos en un repeater
// Los nombres deben ser relativos al contexto del repeater
Forms\Components\Repeater::make('locations')
    ->schema([
        Forms\Components\TextInput::make('latitude'),
        Forms\Components\TextInput::make('longitude'),
        
        MapField::make('map')
            ->latitude('latitude')    // Relativo al repeater
            ->longitude('longitude')  // Relativo al repeater
    ])
```

#### 6. Limpia las cachés

```bash
php artisan filament:cache-components
php artisan view:clear
php artisan cache:clear
npm run build  # Si usas tema personalizado
```

#### 7. Verifica en la consola del navegador

Abre las herramientas de desarrollo (F12) y busca errores en la consola. Los errores comunes incluyen:

- `$wire is not defined` - Problema con Livewire
- `Livewire is not defined` - Livewire no está cargado
- Errores de JavaScript relacionados con Alpine.js

### Solución Temporal: Actualización Manual

Si el problema persiste, puedes actualizar manualmente los campos usando `afterStateUpdated`:

```php
Forms\Components\TextInput::make('latitude')
    ->numeric()
    ->reactive()
    ->afterStateUpdated(function ($state, $set, $get) {
        // Aquí puedes agregar lógica adicional si es necesario
    }),

Forms\Components\TextInput::make('longitude')
    ->numeric()
    ->reactive()
    ->afterStateUpdated(function ($state, $set, $get) {
        // Aquí puedes agregar lógica adicional si es necesario
    }),

MapField::make('map')
    ->latitude('latitude')
    ->longitude('longitude')
```

## 🗺️ El mapa no se muestra

### Síntoma
El espacio del mapa está vacío o muestra un error.

### Soluciones

#### 1. Verifica que `lbcdev/livewire-map-component` esté instalado

```bash
composer require lbcdev/livewire-map-component
```

#### 2. Verifica que Livewire esté funcionando

Crea un componente de prueba simple:

```bash
php artisan make:livewire test-map
```

#### 3. Verifica los assets

Si usas tema personalizado, asegúrate de compilar los assets:

```bash
npm run build
```

## 🎨 Problemas de estilos

### El mapa se ve roto o sin estilos

#### Solución

1. Verifica que Leaflet.css esté cargado (el componente `lbcdev-map` debe incluirlo)
2. Si usas tema personalizado, recompila los assets
3. Limpia la caché del navegador (Ctrl+Shift+R o Cmd+Shift+R)

## 📞 Obtener Ayuda

Si ninguna de estas soluciones funciona:

1. **Verifica la versión de los paquetes**:
   ```bash
   composer show lbcdev/filament-map-field
   composer show lbcdev/livewire-map-component
   composer show filament/filament
   ```

2. **Abre un issue** en GitHub con:
   - Versiones de PHP, Laravel, Filament
   - Código del formulario
   - Mensajes de error completos
   - Capturas de pantalla si es posible

3. **Revisa los issues existentes**: https://github.com/Luinux81/filament-lbcdev-map-field/issues

