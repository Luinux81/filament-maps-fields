<?php

namespace Lbcdev\FilamentMapField;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentMapFieldsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-map-field')
            ->hasViews();
    }

    public function packageBooted(): void
    {
        // Register any additional package logic here if needed
    }
}
