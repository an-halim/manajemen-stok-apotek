<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class StockChart extends ChartWidget
{
    protected static ?string $heading = 'Stock Chart';

    protected static ?string $description = 'Overview of statistics for last 4 months';

    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'out',
                    'data' => [10, 5, 9, 10],
                    'backgroundColor' => 'rgba(39, 55, 245, 0.8)', // Blue border
                    'borderColor' => 'rgba(39, 55, 245, 0.8)', // Blue border
                    'borderWidth' => 1,
                    'borderRadius' => 5,
                ],
                [
                    'label' => 'in',
                    'data' => [5, 51, 12, 89],
                    'backgroundColor' => 'rgba(85, 155, 243, 0.8)', // Light blue background
                    'borderColor' => 'rgba(85, 155, 243, 0.8)', // Light blue background
                    'borderWidth' => 1,
                    'borderRadius' => 5,
                ],
            ],
            'labels' => ['Sep', 'Oct', 'Nov', 'Dec'],
        ];
    }


    protected function getType(): string
    {
        return 'bar';
    }
}
