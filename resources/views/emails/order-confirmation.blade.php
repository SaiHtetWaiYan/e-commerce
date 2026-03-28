<div style="max-width: 600px; margin: 0 auto; font-family: Arial, sans-serif; color: #333;">
    <div style="background-color: #f97316; padding: 24px; text-align: center;">
        <h1 style="color: #fff; margin: 0; font-size: 22px;">Order Confirmed!</h1>
    </div>

    <div style="padding: 24px; background-color: #fff; border: 1px solid #e5e7eb;">
        <p>Hi {{ $order->user->name ?? 'Customer' }},</p>

        <p>Thank you for your order! We've received your order and it's being processed.</p>

        <div style="background-color: #f9fafb; border: 1px solid #e5e7eb; padding: 16px; margin: 16px 0; border-radius: 6px;">
            <table style="width: 100%; font-size: 14px;">
                <tr>
                    <td style="padding: 4px 0; color: #6b7280;">Order Number</td>
                    <td style="padding: 4px 0; text-align: right; font-weight: bold;">{{ $order->order_number }}</td>
                </tr>
                <tr>
                    <td style="padding: 4px 0; color: #6b7280;">Total</td>
                    <td style="padding: 4px 0; text-align: right; font-weight: bold;">${{ number_format($order->total, 2) }}</td>
                </tr>
                <tr>
                    <td style="padding: 4px 0; color: #6b7280;">Payment Method</td>
                    <td style="padding: 4px 0; text-align: right; text-transform: capitalize;">{{ str_replace('_', ' ', $order->payment_method ?? 'Card') }}</td>
                </tr>
                <tr>
                    <td style="padding: 4px 0; color: #6b7280;">Status</td>
                    <td style="padding: 4px 0; text-align: right; text-transform: capitalize;">{{ $order->status->value }}</td>
                </tr>
            </table>
        </div>

        @if ($order->items->isNotEmpty())
            <h3 style="font-size: 16px; margin-bottom: 8px;">Order Items</h3>
            <table style="width: 100%; font-size: 14px; border-collapse: collapse;">
                @foreach ($order->items as $item)
                    <tr style="border-bottom: 1px solid #f3f4f6;">
                        <td style="padding: 8px 0;">{{ $item->product_name }}</td>
                        <td style="padding: 8px 0; text-align: center; color: #6b7280;">x{{ $item->quantity }}</td>
                        <td style="padding: 8px 0; text-align: right;">${{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                @endforeach
            </table>
        @endif

        <p style="margin-top: 20px; font-size: 14px; color: #6b7280;">We'll send you another email when your order ships.</p>
    </div>

    <div style="padding: 16px; text-align: center; font-size: 12px; color: #9ca3af;">
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
    </div>
</div>
