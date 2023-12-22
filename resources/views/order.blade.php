<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hóa Đơn Mua Hàng</title>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9; /* Màu nền nhã nhặn */
            color: #333; /* Màu chữ chính */
        }

        .invoice-box {
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            border: 1px solid #ddd; /* Viền nhẹ */
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1); /* Hiệu ứng đổ bóng nhẹ */
            font-size: 16px;
            line-height: 24px;
            background-color: #fff; /* Màu nền hóa đơn */
        }

        .invoice-title {
            font-size: 2em;
            color: #3498db; /* Màu chữ tiêu đề */
            text-align: center;
            margin-bottom: 20px;
            font-family: 'Arial', sans-serif; /* Font chữ tiêu đề */
        }

        .invoice-details {
            font-size: 1em;
            color: #555; /* Màu chữ thông tin */
        }

        .invoice-details strong {
            color: #333; /* Màu chữ đậm cho thông tin quan trọng */
        }
    </style>
</head>
<body>
    <div class="invoice-box">
        <h1 class="invoice-title">Hóa Đơn Mua Hàng</h1>
        <p class="invoice-details">
            <strong>Người mua:</strong> {{ $invoice->user->name }}<br>
            <strong>Email:</strong> {{ $invoice->user->email }}<br>
            <strong>Số điện thoại:</strong> {{ $invoice->address->mobile }}<br>
            <strong>Số sản phẩm:</strong> {{ $invoice->total_items }}<br>
            <strong>Tổng tiền:</strong> {{ $invoice->total_price }}<br>
            <strong>Ngày tạo hóa đơn:</strong> {{ $invoice->created_at }}<br>
            
            <strong>Địa chỉ nhận hàng:</strong> {{ $invoice->address->flat }}, {{ $invoice->address->area }}, {{ $invoice->address->city }}<br>
        </p>
        <p>Cảm ơn bạn đã mua hàng tại MatViet! Chúng tôi rất mong được phục vụ bạn trong tương lai.</p>
        <p>Trân trọng,</p>
        <p>Đội ngũ MatViet</p>
    </div>
</body>
</html>
