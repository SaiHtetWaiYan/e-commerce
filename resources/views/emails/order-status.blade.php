<div style="max-width: 600px; margin: 0 auto; font-family: Arial, sans-serif; color: #333;">
    <div style="background-color: #f97316; padding: 24px; text-align: center;">
        <h1 style="color: #fff; margin: 0; font-size: 22px;">Order Update</h1>
    </div>

    <div style="padding: 24px; background-color: #fff; border: 1px solid #e5e7eb;">
        <p>Hi {{ $order->user->name ?? 'Customer' }},</p>

        <p>{{ $statusMessage }}</p>

        <div style="background-color: #f9fafb; border: 1px solid #e5e7eb; padding: 16px; margin: 16px 0; border-radius: 6px;">
            <table style="width: 100%; font-size: 14px;">
                <tr>
                    <td style="padding: 4px 0; color: #6b7280;">Order Number</td>
                    <td style="padding: 4px 0; text-align: right; font-weight: bold;">{{ $order->order_number }}</td>
                </tr>
                <tr>
                    <td style="padding: 4px 0; color: #6b7280;">Status</td>
                    <td style="padding: 4px 0; text-align: right; font-weight: bold; text-transform: capitalize;">{{ str_replace('_', ' ', $order->status->value) }}</td>
                </tr>
                <tr>
                    <td style="padding: 4px 0; color: #6b7280;">Total</td>
                    <td style="padding: 4px 0; text-align: right;">${{ number_format($order->total, 2) }}</td>
                </tr>
            </table>
        </div>

        <p style="margin-top: 20px; font-size: 14px; color: #6b7280;">If you have any questions, please contact our support team.</p>
    </div>

    <div style="padding: 16px; text-align: center; font-size: 12px; color: #9ca3af;">
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
    </div>
</div>
