<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\DatePicker;
use Filament\Pages\Dashboard\Actions\FilterAction;
use Filament\Pages\Dashboard\Concerns\HasFiltersAction;

class Dashboard extends \Filament\Pages\Dashboard
{
    use HasFiltersAction;

    protected function getHeaderActions(): array
    {
        return [
            FilterAction::make()
                ->label(__('Filter'))
                ->modalHeading(__('Filter Data'))
                ->modalSubmitActionLabel(__('Terapkan'))
                ->modalCancelActionLabel(__('Batal'))
                ->icon('heroicon-m-funnel')
                ->color('gray')
                ->form([
                    DatePicker::make('startDate')
                        ->label(__('Tanggal Mulai'))
                        ->default(now()->startOfMonth()),
                    DatePicker::make('endDate')
                        ->label(__('Tanggal Selesai'))
                        ->default(now()->endOfMonth()),
                ]),
        ];
    }
}
