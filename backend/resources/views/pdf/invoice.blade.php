<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>HÓA ĐƠN</title>
    <style>
        * {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            page-break-before: avoid;
            page-break-after: avoid;
            page-break-inside: avoid;
        }

        body {
            margin: 0;
            padding: 0;
            overflow: visible !important;
        }

        .container {
            width: 100%;
            padding: 0;
            text-align: center;
        }

        .header {
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 16px;
            margin: 0;
        }

        .header p {
            margin: 0;
            font-size: 10px;
        }

        .section {
            text-align: left;
            margin-bottom: 10px;
        }

        .section-title {
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 5px;
        }

        .line {
            border-top: 1px solid #000;
            margin: 10px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        th,
        td {
            padding: 5px 0;
            vertical-align: top;
        }

        .item-list th {
            border-bottom: 1px dashed #000;
        }

        .item-list td {
            border-bottom: 1px dashed #000;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .summary-table td {
            border: none;
            padding: 2px 0;
        }

        @page {
            size: 80mm 150mm;
            margin: 5mm;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>CỬA HÀNG ĐỒ SHOPGRID</h1>
            <p>Địa chỉ: 123 Đường ABC, TP.HCM</p>
            <p>Điện thoại: 0123.456.789</p>
            <p>Fanpage: facebook.com/shopgrid</p>
        </div>
        <h2 style="font-size: 14px; margin-top: 0; margin-bottom: 10px;">HÓA ĐƠN BÁN HÀNG</h2>

        <div class="section">
            <div><strong>Mã đơn hàng:</strong> #{{ $order_id }}</div>
            <div><strong>Ngày:</strong> {{ $order_time }}</div>
        </div>

        <div class="section">
            <div><strong>Khách hàng:</strong> {{ $guest_name }}</div>
            <div><strong>Điện thoại:</strong> {{ $guest_phone }}</div>
            @if ($guest_email)
                <div><strong>Email:</strong> {{ $guest_email }}</div>
            @endif
            <div><strong>Địa chỉ:</strong> {{ $guest_address }}</div>
        </div>

        <table class="item-list">
            <thead>
                <tr>
                    <th class="text-left">Sản phẩm</th>
                    <th class="text-right">SL</th>
                    <th class="text-right">Giá</th>
                    <th class="text-right">Tổng</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order_details as $detail)
                    <tr>
                        <td class="text-left">
                            {{ $detail['name'] }}
                            @if (count($detail['attributes']) > 0)
                                <br><small>({{ implode(', ', $detail['attributes']) }})</small>
                            @endif
                        </td>
                        <td class="text-right">{{ $detail['quantity'] }}</td>
                        <td class="text-right">{{ number_format($detail['price'], 0) }}₫</td>
                        <td class="text-right">{{ number_format($detail['subtotal'], 0) }}₫</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <table class="summary-table">
            <tbody>
                <tr>
                    <td class="text-right"><strong>Tổng tiền hàng:</strong></td>
                    <td class="text-right">{{ number_format($total_amount - ($shipping_money ?? 0), 0) }}₫</td>
                </tr>
                <tr>
                    <td class="text-right"><strong>Phí vận chuyển:</strong></td>
                    <td class="text-right">{{ number_format($shipping_money ?? 0, 0) }}₫</td>
                </tr>
                <tr>
                    <td class="text-right" style="font-size: 12px; font-weight: bold;">TỔNG THANH TOÁN:</td>
                    <td class="text-right" style="font-size: 12px; font-weight: bold;">
                        {{ number_format($total_amount, 0) }}₫</td>
                </tr>
            </tbody>
        </table>

        <div class="footer" style="margin-top: 20px;">
            <p>CẢM ƠN QUÝ KHÁCH</p>
        </div>
    </div>
</body>

</html>
