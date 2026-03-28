<div style="max-width: 600px; margin: 0 auto; font-family: Arial, sans-serif; color: #333;">
    <div style="background: linear-gradient(135deg, #f97316, #ea580c); padding: 32px 24px; text-align: center; border-radius: 8px 8px 0 0;">
        <h1 style="color: #fff; margin: 0; font-size: 22px;">You left something behind! 🛒</h1>
        <p style="color: rgba(255,255,255,0.85); font-size: 14px; margin: 8px 0 0;">Your cart is waiting for you</p>
    </div>

    <div style="padding: 24px; background-color: #fff; border: 1px solid #e5e7eb; border-top: none;">
        <p>Hi {{ $cart->user->name ?? 'there' }},</p>

        <p>You have {{ $cart->items->count() }} item{{ $cart->items->count() > 1 ? 's' : '' }} in your cart. Don't miss out — come back and complete your purchase!</p>

        @if ($cart->items->isNotEmpty())
            <div style="background-color: #f9fafb; border: 1px solid #e5e7eb; padding: 16px; margin: 16px 0; border-radius: 8px;">
                <table style="width: 100%; font-size: 14px; border-collapse: collapse;">
                    @foreach ($cart->items->take(5) as $item)
                        <tr style="border-bottom: 1px solid #f3f4f6;">
                            <td style="padding: 8px 0;">{{ $item->product->name ?? 'Product' }}</td>
                            <td style="padding: 8px 0; text-align: center; color: #6b7280;">×{{ $item->quantity }}</td>
                            <td style="padding: 8px 0; text-align: right; font-weight: bold;">${{ number_format($item->unit_price * $item->quantity, 2) }}</td>
                        </tr>
                    @endforeach
                </table>
                @if ($cart->items->count() > 5)
                    <p style="font-size: 12px; color: #6b7280; margin: 8px 0 0;">+ {{ $cart->items->count() - 5 }} more item(s)</p>
                @endif
            </div>

            <p style="font-size: 16px; font-weight: bold; color: #111;">
                Cart Total: ${{ number_format($cart->subtotal, 2) }}
            </p>
        @endif

        <div style="text-align: center; margin: 24px 0 16px;">
            <a href="{{ url('/cart') }}" style="display: inline-block; background-color: #f97316; color: #fff; padding: 14px 32px; border-radius: 8px; text-decoration: none; font-weight: bold; font-size: 14px;">Complete Your Purchase</a>
        </div>

        <p style="font-size: 13px; color: #6b7280;">Items in your cart may sell out. Complete checkout soon to secure them!</p>
    </div>

    <div style="padding: 16px; text-align: center; font-size: 12px; color: #9ca3af; border: 1px solid #e5e7eb; border-top: none; border-radius: 0 0 8px 8px; background: #fafafa;">
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
    </div>
</div>
