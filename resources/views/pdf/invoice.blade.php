<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<title>Invoice #{{ $sale->invoice_number }}</title>
  <style>
    @media print {
      .page-break {
        display: block;
        page-break-before: always;
      }
    }

    #invoice-POS {
      box-shadow: 0 0 1in -0.25in rgba(0, 0, 0, 0.5);
      padding: 2mm;
      margin: 0 auto;
      width: 44mm;
      background: #FFF;
    }

    #invoice-POS h1,
    #invoice-POS h2,
    #invoice-POS h3,
    #invoice-POS p {
      margin: 0;
      padding: 0;
      color: #222;
      text-align: center;
    }

    #invoice-POS h1 {
      font-size: 1.5em;
    }

    #invoice-POS h2 {
      font-size: 0.9em;
    }

    #invoice-POS h3 {
      font-size: 1.2em;
      line-height: 2em;
    }

    #invoice-POS p {
      font-size: 0.7em;
      line-height: 1.2em;
    }

    #invoice-POS #top,
    #invoice-POS #mid,
    #invoice-POS #bot {
      border-bottom: 1px solid #EEE;
      padding: 5px 0;
    }

    #invoice-POS .info,
    #invoice-POS .title {
      text-align: center;
    }

    #invoice-POS table {
      width: 100%;
      border-collapse: collapse;
    }

    #invoice-POS .tabletitle {
      background: #EEE;
      font-size: 0.5em;
    }

    #invoice-POS .item td {
      border-bottom: 1px solid #EEE;
      padding: 5px;
      text-align: center;
      font-size: 0.5em;
    }

    #invoice-POS #legalcopy {
      margin-top: 5mm;
      text-align: center;
    }
    body.receipt .sheet { width: 58mm; height: 100mm } /* change height as you like */
    @media print { body.receipt { width: 58mm } }
  </style>
</head>

<body class="receipt">
  <div id="invoice-POS" class="sheet">
    <center id="top">
      <div class="info">
        <h1>Apotek Bunda Farma</h1>
        <p>Address: Custom Street, Custom City, 12345<br>Phone: 123-456-7890</p>
      </div>
    </center>

    <div id="bot">
      <table>
        <thead>
            <tr class="tabletitle">
                <th>Item</th>
                <th>Qty</th>
                <th>Price/Unit</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @php $grandTotal = 0; @endphp
            @foreach ($sale->items as $item)
                @php
                    $total = $item->sale_quantity * $item->selling_price;
                    $grandTotal += $total;
                @endphp
                <tr class="item">
                    <td>{{ $item->product->name ?? 'N/A' }}</td>
                    <td>{{ $item->sale_quantity }}</td>
                    <td>Rp {{ number_format($item->selling_price, 2, ',', '.') }}</td>
                    <td>Rp {{ number_format($total, 2, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="tabletitle">
                <td colspan="3" class="total">Grand Total:</td>
                <td class="total">${{ number_format($grandTotal, 2, ',', '.') }}</td>
            </tr>
        </tfoot>
      </table>

      <div id="legalcopy">
        <p>
            <strong>
            Terimakasih atas kunjungan anda!
            </strong>
            <br>
            Barang yang sudah di beli tidak dapat dikembalikan
        </p>
      </div>
    </div>
  </div>
</body>

</html>
