<?php

namespace App\Http\Controllers;

use App\Models\sale;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class SaleController extends Controller
{

    public function printInvoice($id)
    {
        $sale = Sale::with(['items.product'])->findOrFail($id);

        $pdf = Pdf::loadView('pdf.invoice', compact('sale'));
        return $pdf->stream('invoice-' . $sale->invoice_number . '.pdf');
    }
}
