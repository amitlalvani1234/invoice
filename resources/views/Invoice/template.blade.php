<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <style>
        /* Styles for the invoice */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100%;
            margin: 20px auto;
            border: 1px solid #ccc;
            padding: 20px;
        }

        .invoice-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .invoice-header h1 {
            margin: 0;
            color: #333;
        }

        .invoice-details {
            margin-bottom: 20px;
        }

        .invoice-details table {
            width: 100%;
            border-collapse: collapse;
        }

        .invoice-details table th,
        .invoice-details table td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }

        .invoice-items table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .invoice-items table th,
        .invoice-items table td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }

        .invoice-total {
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="invoice-header">
            <h1>Invoice</h1>
        </div>
        <div class="invoice-details">
            <table>
                <tr>
                    <th>Invoice Number:</th>
                    <td>{{ $invoiceNumber }}</td>
                </tr>
                <tr>
                    <th>Invoice Date:</th>
                    <td>{{ $invoiceDate }}</td>
                </tr>
                <tr>
                    <th>Customer Name:</th>
                    <td>{{ $customerName }}</td>
                </tr>
                <tr>
                    <th>Customer Number:</th>
                    <td>{{ $customerNo }}</td>
                </tr>
            </table>
        </div>
        <div class="invoice-items">
            <table>
                <thead>
                    <tr>
                        <th>Description</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($invoiceItems as $invoiceItem)
                        <tr>
                            <td>{{ $invoiceItem->item_description }}</td>
                            <td>{{ $invoiceItem->qty }}</td>
                            <td>{{ $invoiceItem->price }}</td>
                            <td>{{ $invoiceItem->total }}</td>
                        </tr>
                    @endforeach

                </tbody>
            </table>
        </div>
        <div class="invoice-total">
            <p><strong>Subtotal:</strong> {{ $subtotal }}</p>
            <p><strong>Tax ({{ $gst }}%):</strong> {{ $gst_amount }}</p>
            <p><strong>Total:</strong> {{ $grand_total }}</p>
        </div>
    </div>
</body>

</html>
