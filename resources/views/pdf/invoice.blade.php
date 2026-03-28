<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $order->order_number }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            text-align: center;
            color: #333;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            font-size: 14px;
            line-height: 24px;
            text-align: left;
        }
        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
            border-collapse: collapse;
        }
        .invoice-box table td {
            padding: 5px;
            vertical-align: top;
        }
        .invoice-box table tr td:nth-child(2) {
            text-align: right;
        }
        .invoice-box table tr.top table td {
            padding-bottom: 20px;
        }
        .invoice-box table tr.top table td.title {
            font-size: 45px;
            line-height: 45px;
            color: #333;
        }
        .invoice-box table tr.information table td {
            padding-bottom: 40px;
        }
        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }
        .invoice-box table tr.details td {
            padding-bottom: 20px;
        }
        .invoice-box table tr.item td {
            border-bottom: 1px solid #eee;
        }
        .invoice-box table tr.item.last td {
            border-bottom: none;
        }
        .invoice-box table tr.total td:last-child {
            border-top: 2px solid #eee;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="invoice-box">
        <table cellpadding="0" cellspacing="0">
            <tr class="top">
                <td colspan="4">
                    <table>
                        <tr>
                            <td class="title">
                                Invoice
                            </td>
                            <td>
                                Order #: {{ $order->order_number }}<br>
                                Created: {{ $order->created_at->format('M d, Y') }}<br>
                                Paid On: {{ $order->paid_at ? $order->paid_at->format('M d, Y') : 'Pending' }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="information">
                <td colspan="4">
                    <table>
                        <tr>
                            <td>
                                <strong>Billed To:</strong><br>
                                {{ $order->user->name }}<br>
                                {{ $order->user->email }}
                            </td>
                            <td>
                                <strong>Shipped To:</strong><br>
                                @if (is_array($order->shipping_address))
                                    {{ $order->shipping_address['full_name'] ?? $order->shipping_address['name'] ?? '' }}<br>
                                    {{ $order->shipping_address['street_address'] ?? $order->shipping_address['address_line_1'] ?? $order->shipping_address['street'] ?? '' }}<br>
                                    {{ $order->shipping_address['city'] ?? '' }} {{ $order->shipping_address['postal_code'] ?? $order->shipping_address['zip'] ?? '' }}
                                @else
                                    {{ $order->shipping_address }}
                                @endif
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="heading">
                <td>Item</td>
                <td style="text-align: center;">Price</td>
                <td style="text-align: center;">Quantity</td>
                <td style="text-align: right;">Total</td>
            </tr>

            @foreach($order->items as $item)
            <tr class="item {{ $loop->last ? 'last' : '' }}">
                <td style="text-align: left;">
                    {{ $item->product_name }}
                    @if($item->variant_name)
                        <br><small>{{ $item->variant_name }}</small>
                    @endif
                </td>
                <td style="text-align: center;">${{ number_format($item->unit_price, 2) }}</td>
                <td style="text-align: center;">{{ $item->quantity }}</td>
                <td style="text-align: right;">${{ number_format($item->subtotal, 2) }}</td>
            </tr>
            @endforeach

            <tr class="total">
                <td colspan="3" style="text-align: right;">Subtotal:</td>
                <td>${{ number_format($order->subtotal, 2) }}</td>
            </tr>
            @if((float) $order->discount_amount > 0)
            <tr class="total">
                <td colspan="3" style="text-align: right;">Discount:</td>
                <td>-${{ number_format($order->discount_amount, 2) }}</td>
            </tr>
            @endif
            <tr class="total">
                <td colspan="3" style="text-align: right;">Shipping:</td>
                <td>${{ number_format($order->shipping_fee, 2) }}</td>
            </tr>
            @if((float) $order->tax_amount > 0)
            <tr class="total">
                <td colspan="3" style="text-align: right;">Tax:</td>
                <td>${{ number_format($order->tax_amount, 2) }}</td>
            </tr>
            @endif
            <tr class="total">
                <td colspan="3" style="text-align: right;"><strong>Total:</strong></td>
                <td><strong>${{ number_format($order->total, 2) }}</strong></td>
            </tr>
        </table>
    </div>
</body>
</html>
