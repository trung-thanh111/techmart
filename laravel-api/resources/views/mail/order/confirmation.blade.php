<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác nhận đơn hàng #{{ $order->code }}</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f3f4f6;
            color: #374151;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #0284c7, #0369a1);
            color: #ffffff;
            padding: 24px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            letter-spacing: 0.5px;
        }
        .header p {
            margin: 8px 0 0;
            font-size: 14px;
            opacity: 0.9;
        }
        .content {
            padding: 24px;
        }
        .greeting {
            font-size: 16px;
            margin-bottom: 16px;
        }
        .card {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 16px;
            margin-bottom: 20px;
        }
        .card-title {
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            color: #4b5563;
            margin-bottom: 10px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 6px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 6px;
            font-size: 14px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 16px 0;
            font-size: 14px;
        }
        .table th {
            background: #f3f4f6;
            color: #374151;
            padding: 10px 8px;
            text-align: left;
            border-bottom: 2px solid #e5e7eb;
        }
        .table td {
            padding: 10px 8px;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: middle;
        }
        .table .text-right {
            text-align: right;
        }
        .table .text-center {
            text-align: center;
        }
        .totals {
            margin-top: 12px;
            border-top: 2px solid #e5e7eb;
            padding-top: 10px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            margin-bottom: 6px;
        }
        .total-final {
            display: flex;
            justify-content: space-between;
            font-size: 18px;
            font-weight: bold;
            color: #dc2626;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px dashed #d1d5db;
        }
        .btn-wrapper {
            text-align: center;
            margin: 28px 0 16px;
        }
        .btn {
            display: inline-block;
            background: #0284c7;
            color: #ffffff;
            padding: 12px 28px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
        }
        .footer {
            background: #f9fafb;
            padding: 16px;
            text-align: center;
            font-size: 12px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>TECHMART</h1>
            <p>Cảm ơn bạn đã mua sắm cùng chúng tôi!</p>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="greeting">
                Xin chào <strong>{{ $order->customer_name }}</strong>,
                <br>
                Đơn hàng <strong>#{{ $order->code }}</strong> của bạn đã được tiếp nhận và đang được xử lý.
            </div>

            <!-- Order Meta -->
            <div class="card">
                <div class="card-title">Thông tin giao hàng</div>
                <div class="info-row">
                    <span><strong>Người nhận:</strong></span>
                    <span>{{ $order->customer_name }}</span>
                </div>
                <div class="info-row">
                    <span><strong>Số điện thoại:</strong></span>
                    <span>{{ $order->customer_phone }}</span>
                </div>
                <div class="info-row">
                    <span><strong>Địa chỉ nhận hàng:</strong></span>
                    <span>{{ $order->shipping_address }}</span>
                </div>
                <div class="info-row">
                    <span><strong>Phương thức thanh toán:</strong></span>
                    <span style="text-transform: uppercase;">{{ $order->payment_method }}</span>
                </div>
                @if(!empty($order->customer_note))
                <div class="info-row">
                    <span><strong>Ghi chú:</strong></span>
                    <span>{{ $order->customer_note }}</span>
                </div>
                @endif
            </div>

            <!-- Order Items -->
            <table class="table">
                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th class="text-center">SL</th>
                        <th class="text-right">Đơn giá</th>
                        <th class="text-right">Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                    <tr>
                        <td>
                            <strong>{{ $item->product_name }}</strong>
                            @if(!empty($item->product_sku))
                            <div style="font-size: 12px; color: #6b7280;">SKU: {{ $item->product_sku }}</div>
                            @endif
                        </td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-right">{{ number_format($item->unit_price, 0, ',', '.') }} đ</td>
                        <td class="text-right"><strong>{{ number_format($item->total ?? ($item->unit_price * $item->quantity), 0, ',', '.') }} đ</strong></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Totals -->
            <div class="totals">
                <div class="total-row">
                    <span>Tạm tính:</span>
                    <span>{{ number_format($order->subtotal, 0, ',', '.') }} đ</span>
                </div>
                @if($order->discount_amount > 0)
                <div class="total-row" style="color: #16a34a;">
                    <span>Giảm giá khuyến mãi:</span>
                    <span>-{{ number_format($order->discount_amount, 0, ',', '.') }} đ</span>
                </div>
                @endif
                <div class="total-row">
                    <span>Phí vận chuyển:</span>
                    <span>{{ number_format($order->shipping_fee, 0, ',', '.') }} đ</span>
                </div>
                @if($order->tax_amount > 0)
                <div class="total-row">
                    <span>Thuế VAT:</span>
                    <span>{{ number_format($order->tax_amount, 0, ',', '.') }} đ</span>
                </div>
                @endif
                <div class="total-final">
                    <span>Tổng thanh toán:</span>
                    <span>{{ number_format($order->total_amount, 0, ',', '.') }} đ</span>
                </div>
            </div>

            <!-- CTA Button -->
            <div class="btn-wrapper">
                <a href="{{ config('app.url', 'http://localhost:3000') }}" class="btn" style="color: #ffffff;">Tiếp tục mua sắm</a>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Email này được gửi tự động từ hệ thống TechMart.</p>
            <p>Mọi thắc mắc xin vui lòng liên hệ hotline hỗ trợ hoặc phản hồi lại email này.</p>
        </div>
    </div>
</body>
</html>
