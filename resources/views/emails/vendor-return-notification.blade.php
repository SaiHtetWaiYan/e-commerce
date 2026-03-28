<div style="max-width: 600px; margin: 0 auto; font-family: Arial, sans-serif; color: #333;">
    <div style="background-color: #f97316; padding: 24px; text-align: center;">
        <h1 style="color: #fff; margin: 0; font-size: 22px;">New Return Request</h1>
    </div>

    <div style="padding: 24px; background-color: #fff; border: 1px solid #e5e7eb;">
        <p>Hi {{ $vendorName }},</p>

        <p>A customer has requested a return for items from one of your orders.</p>

        <div style="background-color: #f9fafb; border: 1px solid #e5e7eb; padding: 16px; margin: 16px 0; border-radius: 6px;">
            <table style="width: 100%; font-size: 14px;">
                <tr>
                    <td style="padding: 4px 0; color: #6b7280;">Order Number</td>
                    <td style="padding: 4px 0; text-align: right; font-weight: bold;">{{ $returnRequest->order->order_number }}</td>
                </tr>
                <tr>
                    <td style="padding: 4px 0; color: #6b7280;">Return Reason</td>
                    <td style="padding: 4px 0; text-align: right;">{{ $returnRequest->reason }}</td>
                </tr>
            </table>
        </div>

        @if ($returnRequest->items->isNotEmpty())
            <h3 style="font-size: 16px; margin: 20px 0 12px;">Items Being Returned</h3>
            <div style="background-color: #f9fafb; border: 1px solid #e5e7eb; padding: 16px; border-radius: 6px;">
                <table style="width: 100%; font-size: 14px; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th style="padding: 8px 4px; text-align: left; color: #6b7280; border-bottom: 1px solid #e5e7eb;">Product</th>
                            <th style="padding: 8px 4px; text-align: center; color: #6b7280; border-bottom: 1px solid #e5e7eb;">Qty</th>
                            <th style="padding: 8px 4px; text-align: right; color: #6b7280; border-bottom: 1px solid #e5e7eb;">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($returnRequest->items as $item)
                            <tr>
                                <td style="padding: 8px 4px;">{{ $item->orderItem->product_name ?? 'N/A' }}</td>
                                <td style="padding: 8px 4px; text-align: center;">{{ $item->quantity }}</td>
                                <td style="padding: 8px 4px; text-align: right;">${{ number_format($item->subtotal, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <p style="margin-top: 20px; font-size: 14px; color: #6b7280;">Please review this return request in your vendor dashboard.</p>
    </div>

    <div style="padding: 16px; text-align: center; font-size: 12px; color: #9ca3af;">
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
    </div>
</div>
