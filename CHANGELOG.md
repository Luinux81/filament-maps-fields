# Changelog

All notable changes to `filament-maps-fields` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.0.0] - 2026-02-17

### Added

- Initial stable release
- `MapField` form component for selecting map coordinates
  - JSON mode (recommended): stores coordinates as `{"latitude": X, "longitude": Y}`
  - Legacy mode: separate fields for backward compatibility
  - Click on map to select location
  - Paste coordinates from clipboard
  - Read-only mode support
  - Dot notation support for nested data
  - Integrated Filament validation
- `MapBoundsField` form component for selecting map bounds
  - Manages northEast and southWest bounds
  - JSON and legacy modes
  - Full validation support
- `MapEntry` infolist component for displaying locations
  - Read-only map display
  - JSON and legacy mode support
- `MapBoundsEntry` infolist component for displaying bounds
  - Read-only bounds display
- `FilamentMapsFieldsServiceProvider` with auto-discovery
- Integration with `LivewireMap` from `lbcdev/livewire-maps-core`
- Integration with `Marker` and `MarkerCollection` from `lbcdev/map-geometries`
- Comprehensive test suite (11 test files)
  - `MapFieldTest` - Basic field tests
  - `MapFieldJsonModeTest` - JSON mode tests
  - `MapFieldJsonNotationTest` - Dot notation tests
  - `MapFieldRequiredValidationTest` - Required validation
  - `MapFieldBackwardCompatibilityTest` - Legacy compatibility
  - `MapBoundsFieldTest` - Basic bounds tests
  - `MapBoundsFieldJsonModeTest` - JSON mode for bounds
  - `MapBoundsFieldJsonNotationTest` - Dot notation for bounds
  - `MapBoundsFieldRequiredValidationTest` - Bounds validation
  - `MapEntryTest` - Infolist entry tests
  - `MapBoundsEntryTest` - Bounds entry tests
- Complete documentation
  - `README.md` - Installation, configuration and full API
  - `EXAMPLES.md` - Usage examples
  - `TROUBLESHOOTING.md` - Common issues and solutions

[Unreleased]: https://github.com/Luinux81/filament-maps-fields/compare/v1.0.0...HEAD
[1.0.0]: https://github.com/Luinux81/filament-maps-fields/releases/tag/v1.0.0
