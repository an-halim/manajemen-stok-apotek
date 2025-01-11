<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class ProductBestSeller extends ChartWidget
{
    protected static ?string $heading = 'Best Seller Products';

    protected static ?string $description = 'Overview of top 5 best selling products';

    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'data' => [1000, 5, 9, 10],
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
            'labels' => ['Paratusin 500gram', 'Panadol 100gram', 'Betadine 50gram', 'Caviplex 100gram'],
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
