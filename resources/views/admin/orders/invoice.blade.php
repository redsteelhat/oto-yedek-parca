<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fatura - {{ $order->order_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border: 1px solid #ddd;
        }
        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .company-info {
            flex: 1;
        }
        .invoice-info {
            text-align: right;
        }
        .invoice-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .invoice-number {
            font-size: 18px;
            color: #666;
        }
        .section {
            margin-bottom: 30px;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .two-columns {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table th, table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .total-row {
            font-weight: bold;
            font-size: 18px;
            background-color: #f5f5f5;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #666;
            font-size: 12px;
        }
        @media print {
            body {
                padding: 0;
            }
            .invoice-container {
                border: none;
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="header">
            <div class="company-info">
                <h1 class="invoice-title">FATURA</h1>
                <p><strong>Firma Adı:</strong> {{ \App\Models\Setting::getValue('site_name', 'Yedek Parça Mağazası') }}</p>
                <p><strong>Adres:</strong> {{ \App\Models\Setting::getValue('site_address', '') }}</p>
                <p><strong>Telefon:</strong> {{ \App\Models\Setting::getValue('site_phone', '') }}</p>
                <p><strong>E-posta:</strong> {{ \App\Models\Setting::getValue('site_email', '') }}</p>
            </div>
            <div class="invoice-info">
                <p class="invoice-number">Fatura No: {{ $order->order_number }}</p>
                <p><strong>Tarih:</strong> {{ $order->created_at->format('d.m.Y') }}</p>
                <p><strong>Saat:</strong> {{ $order->created_at->format('H:i') }}</p>
            </div>
        </div>

        <div class="section">
            <div class="two-columns">
                <div>
                    <div class="section-title">Fatura Adresi</div>
                    <p><strong>{{ $order->billing_name ?? $order->shipping_name }}</strong></p>
                    <p>{{ $order->billing_address ?? $order->shipping_address }}</p>
                    <p>{{ $order->billing_district ?? $order->shipping_district }} / {{ $order->billing_city ?? $order->shipping_city }}</p>
                    <p>{{ $order->billing_postal_code ?? $order->shipping_postal_code }}</p>
                    <p>Tel: {{ $order->billing_phone ?? $order->shipping_phone }}</p>
                </div>
                <div>
                    <div class="section-title">Teslimat Adresi</div>
                    <p><strong>{{ $order->shipping_name }}</strong></p>
                    <p>{{ $order->shipping_address }}</p>
                    <p>{{ $order->shipping_district }} / {{ $order->shipping_city }}</p>
                    <p>{{ $order->shipping_postal_code }}</p>
                    <p>Tel: {{ $order->shipping_phone }}</p>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Sipariş Detayları</div>
            <table>
                <thead>
                    <tr>
                        <th>Ürün</th>
                        <th>SKU</th>
                        <th class="text-right">Adet</th>
                        <th class="text-right">Birim Fiyat</th>
                        <th class="text-right">KDV</th>
                        <th class="text-right">Toplam</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                        <tr>
                            <td>{{ $item->product_name }}</td>
                            <td>{{ $item->product_sku }}</td>
                            <td class="text-right">{{ $item->quantity }}</td>
                            <td class="text-right">{{ number_format($item->price, 2) }} ₺</td>
                            <td class="text-right">%{{ number_format($item->tax_rate, 0) }}</td>
                            <td class="text-right">{{ number_format($item->total, 2) }} ₺</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5" class="text-right"><strong>Ara Toplam:</strong></td>
                        <td class="text-right"><strong>{{ number_format($order->subtotal, 2) }} ₺</strong></td>
                    </tr>
                    @if($order->discount_amount > 0)
                        <tr>
                            <td colspan="5" class="text-right text-red-600"><strong>İndirim:</strong></td>
                            <td class="text-right text-red-600"><strong>-{{ number_format($order->discount_amount, 2) }} ₺</strong></td>
                        </tr>
                    @endif
                    <tr>
                        <td colspan="5" class="text-right"><strong>KDV:</strong></td>
                        <td class="text-right"><strong>{{ number_format($order->tax_amount, 2) }} ₺</strong></td>
                    </tr>
                    <tr>
                        <td colspan="5" class="text-right"><strong>Kargo:</strong></td>
                        <td class="text-right"><strong>{{ number_format($order->shipping_cost, 2) }} ₺</strong></td>
                    </tr>
                    <tr class="total-row">
                        <td colspan="5" class="text-right"><strong>GENEL TOPLAM:</strong></td>
                        <td class="text-right"><strong>{{ number_format($order->total, 2) }} ₺</strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="section">
            <div class="section-title">Ödeme Bilgileri</div>
            <p><strong>Ödeme Yöntemi:</strong> {{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</p>
            <p><strong>Ödeme Durumu:</strong> {{ ucfirst($order->payment_status) }}</p>
            @if($order->payment_transaction_id)
                <p><strong>İşlem No:</strong> {{ $order->payment_transaction_id }}</p>
            @endif
            @if($order->coupon_code)
                <p><strong>Kullanılan Kupon:</strong> {{ $order->coupon_code }}</p>
            @endif
        </div>

        @if($order->notes)
            <div class="section">
                <div class="section-title">Notlar</div>
                <p>{{ $order->notes }}</p>
            </div>
        @endif

        <div class="footer">
            <p>Bu fatura elektronik olarak oluşturulmuştur.</p>
            <p>{{ \App\Models\Setting::getValue('site_name', 'Yedek Parça Mağazası') }} - {{ date('Y') }}</p>
        </div>
    </div>
</body>
</html>

