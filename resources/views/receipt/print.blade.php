<!-- resources/views/receipt/print.blade.php -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pembelian</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            margin: 0;
            padding: 0;
            background-color: #f3f4f6;
        }
        .receipt-container {
            max-width: 300px;
            margin: 20px auto;
            background: white;
            padding: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .receipt-header {
            text-align: center;
            border-bottom: 1px dashed #ddd;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        .receipt-header h1 {
            font-size: 18px;
            margin: 0;
        }
        .store-info {
            text-align: center;
            font-size: 12px;
            margin-bottom: 10px;
        }
        .receipt-details {
            margin-bottom: 15px;
            font-size: 12px;
        }
        .receipt-details div {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 12px;
        }
        .items-table th {
            border-bottom: 1px solid #ddd;
            text-align: left;
            padding: 3px 5px;
        }
        .items-table td {
            padding: 3px 0;
        }
        .total-section {
            border-top: 1px dashed #ddd;
            padding-top: 10px;
            font-size: 12px;
        }
        .total-section div {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }
        .total-amount {
            font-weight: bold;
            font-size: 14px;
        }
        .receipt-footer {
            text-align: center;
            margin-top: 15px;
            font-size: 10px;
            color: #666;
        }
        .receipt-actions {
            text-align: center;
            margin-top: 20px;
        }
        .print-btn {
            background-color: #3b82f6;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        @media print {
            .receipt-actions {
                display: none;
            }
            body {
                background-color: white;
            }
            .receipt-container {
                box-shadow: none;
                max-width: 100%;
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="receipt-header">
            <h1>STRUK PEMBELIAN</h1>
        </div>
        
        <div class="store-info">
            <p>TOKO SERBAGUNA</p>
            <p>Jl. Merapi Raya Kota Depok</p>
            <p>Telp: (021) 1234-5678</p>
        </div>
        
        <div class="receipt-details">
            <div>
                <span>Tanggal:</span>
                <span>{{ session('receipt')['date'] }}</span>
            </div>
            <div>
                <span>Pelanggan:</span>
                <span>{{ session('receipt')['name_customer'] }}</span>
            </div>
            <div>
                <span>Metode Pembayaran:</span>
                <span>{{ session('receipt')['payment_method'] }}</span>
            </div>
        </div>
        
        <table class="items-table">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Qty</th>
                    <th>Unit Price</th>
                </tr>
            </thead>
            <tbody>
                @foreach(session('receipt')['items'] as $item)
                <tr>
                    <td>{{ $item['name'] }}</td>
                    <td>{{ $item['quantity'] }}</td>
                    <td>Rp {{ number_format($item['discounted_price'], 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <div class="total-section">
            <div>
                <span>Subtotal:</span>
                <span>Rp {{ number_format($item['discounted_price'] * $item['quantity'], 0, ',', '.') }}</span>
            </div>
            @if(session('receipt')['discount'] > 0)
            <div>
                <span>Diskon:</span>
                <span>Rp {{ number_format(session('receipt')['discount'], 0, ',', '.') }}</span>
            </div>
            @endif
            <div class="total-amount">
                <span>TOTAL:</span>
            <span>Rp {{ number_format(session('receipt')['total_price'], 0, ',', '.') }}</span>
            </div>
        </div>
        
        <div class="receipt-footer">
            <p>Terima kasih atas kunjungan Anda!</p>
            <p>Barang yang sudah dibeli tidak dapat dikembalikan</p>
        </div>
    </div>
    <div class="receipt-actions">
    <button class="print-btn" onclick="window.print(); setTimeout(() => window.location.href = '/', 1000);">
        Cetak Struk
    </button>
    <button class="print-btn" style="background-color:rgb(0, 85, 255); margin-left: 10px;" onclick="window.location.href = '/user/pos-page';">
        Kembali
    </button>
</div>
</body>