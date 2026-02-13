{{-- 
    Leaflet.js Assets for Filament Maps Fields
    
    This view is designed to be used with Filament's renderHook feature
    to inject Leaflet.js assets into the admin panel head.
    
    Usage in AdminPanelProvider:
    ->renderHook(
        'panels::head.end',
        fn(): string => view('filament-maps-fields::hooks.leaflet-assets')->render()
    )
--}}

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" 
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" 
      crossorigin="" />

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" 
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" 
        crossorigin=""></script>

