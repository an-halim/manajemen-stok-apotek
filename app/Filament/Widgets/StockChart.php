<?php

namespace App\Filament\Widgets;

use App\Models\PurchaseItem;
use App\Models\SaleItem;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class StockChart extends ChartWidget
{
    protected static ?string $heading = 'Grafik Stok';

    protected static ?string $description = 'Ikhtisar statistik untuk 4 bulan terakhir';

    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        // Get the last 4 months dynamically
        $months = collect(range(3, 0))->mapWithKeys(function ($i) {
            $date = Carbon::now()->subMonths($i);
            return [$date->format('n') => $date->format('M')]; // '9' => 'Sep'
        });

         // Call Model Methods 🚀
        $purchaseData = PurchaseItem::getMonthlyPurchaseData(3);
        $salesData = SaleItem::getMonthlySalesData(3);

        // Map data to corresponding labels
        $inData = [];
        $outData = [];

        foreach ($months as $monthNum => $monthName) {
            $inData[] = $purchaseData[$monthNum] ?? 0; // Default to 0 if no data
            $outData[] = $salesData[$monthNum] ?? 0; // Default to 0 if no data
        }

        return [
            'datasets' => [
                [
                    'label' => 'Out',
                    'data' => $outData,
                    'backgroundColor' => 'rgba(39, 55, 245, 0.8)',
                    'borderColor' => 'rgba(39, 55, 245, 0.8)',
                    'borderWidth' => 1,
                    'borderRadius' => 5,
                ],
                [
                    'label' => 'In',
                    'data' => $inData,
                    'backgroundColor' => 'rgba(85, 155, 243, 0.8)',
                    'borderColor' => 'rgba(85, 155, 243, 0.8)',
                    'borderWidth' => 1,
                    'borderRadius' => 5,
                ],
            ],
            'labels' => $months->values()->all(),
        ];
    }


    protected function getType(): string
    {
        return 'bar';
    }
}
