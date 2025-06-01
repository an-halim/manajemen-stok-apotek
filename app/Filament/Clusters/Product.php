<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class Product extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?string $navigationLabel = 'Produk';
}
