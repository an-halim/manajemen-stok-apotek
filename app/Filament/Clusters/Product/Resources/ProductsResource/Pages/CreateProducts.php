<?php

namespace App\Filament\Clusters\Product\Resources\ProductsResource\Pages;

use App\Filament\Clusters\Product\Resources\ProductsResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProducts extends CreateRecord
{
    protected static string $resource = ProductsResource::class;
}
