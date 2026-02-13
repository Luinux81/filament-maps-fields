<?php

namespace LBCDev\FilamentMapsFields;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

/**
 * Service Provider for Filament Maps Fields
 * 
 * This package provides Filament form components and infolist entries
 * for working with interactive maps, built on top of the
 * lbcdev/livewire-maps-core package.
 * 
 * @package LBCDev\FilamentMapsFields
 */
class FilamentMapsFieldsServiceProvider extends PackageServiceProvider
{
    /**
     * Configure the package
     * 
     * @param Package $package
     * @return void
     */
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-maps-fields')
            ->hasViews();
    }

    /**
     * Package booted callback
     * 
     * This method is called after the package has been registered
     * and all service providers have been loaded.
     * 
     * @return void
     */
    public function packageBooted(): void
    {
        // Any additional package logic can be added here
        // For example: registering custom Blade directives,
        // publishing assets, etc.
    }
}
