<?php

namespace App\Filament\Widgets;

use App\Models\SaleItem;
use Filament\Widgets\ChartWidget;

class ProductBestSeller extends ChartWidget
{
    protected static ?string $heading = 'Produk Terlaris';

    protected static ?string $description = 'Gambaran umum dari 5 produk terlaris';

    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {

        $topProducts = SaleItem::getTopSellingProducts();

        return [
            'datasets' => [
                [
                    'data' => $topProducts->pluck('total_sold')->toArray(),
                    'backgroundColor'=> [
                        'rgb(255, 99, 132)',
                        'rgb(54, 162, 235)',
                        'rgb(255, 205, 86)',
                        'rgba(39, 55, 245, 0.8)',
                      ],
                    'borderWidth' => 1,
                    'borderRadius' => 5,
                ]
            ],
            'labels' => $topProducts->pluck('product_name')->toArray(),
        ];
    }

    protected static ?array $options = [
        'plugins' => [
            'legend' => [
                'display' => true,
            ],
        ],
    ];

    protected function getType(): string
    {
        return 'pie';
    }
}
