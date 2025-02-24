<?php

namespace App\Filament\Resources\SaleResource\Pages;

use App\Filament\Resources\SaleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Barryvdh\DomPDF\Facade\Pdf;

class EditSale extends EditRecord
{
    protected static string $resource = SaleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\Action::make('print_pdf')
            ->label('Print Invoice')
            ->icon('heroicon-o-printer')
            ->color('primary')
            // ->action(function ($record) {
            //     $pdf = Pdf::loadView('pdf.invoice', ['sale' => $record]);
            //     $fileName = 'invoice-' . $record->invoice_number . '.pdf';


            //     return response()->streamDownload(
            //         fn () => print($pdf->output()),
            //         $fileName
            //     );
            // }),
            ->url(fn ($record) => route('invoice.print', $record)) // ✅ Open new tab for printing
            ->openUrlInNewTab(),
        ];
    }
}
