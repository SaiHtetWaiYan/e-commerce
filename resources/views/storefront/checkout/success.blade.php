<x-layouts.app>
    <div class="max-w-[600px] mx-auto px-4 py-8">
        <div class="bg-white border border-gray-200">
            <!-- Header -->
            @if ($order->payment_status->value === 'failed')
                <div class="bg-red-600 px-6 py-8 text-center">
                    <div class="mx-auto w-14 h-14 bg-white flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                    </div>
                    <h1 class="text-xl font-bold text-white mb-1">Payment Failed</h1>
                    <p class="text-red-100 text-sm">Your order has been created but payment was not completed.</p>
                </div>
            @else
                <div class="bg-green-600 px-6 py-8 text-center">
                    <div class="mx-auto w-14 h-14 bg-white flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <h1 class="text-xl font-bold text-white mb-1">Order Confirmed!</h1>
                    <p class="text-green-100 text-sm">Thank you for your purchase.</p>
                </div>
            @endif

            <!-- Order Details -->
            <div class="p-6">
                <div class="text-center mb-6">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Order Number</p>
                    <p class="text-xl font-bold text-gray-900 font-mono">{{ $order->order_number }}</p>
                </div>

                @if (session('stripe_error'))
                    <div class="bg-yellow-50 border border-yellow-200 p-4 mb-6">
                        <h3 class="text-xs font-bold text-yellow-800 uppercase tracking-wide mb-2">Payment Notice</h3>
                        <p class="text-xs text-yellow-700">{{ session('stripe_error') }}</p>
                    </div>
                @endif

                @if ($order->payment_method === 'card' && $order->payment_status->value === 'pending')
                    <div class="bg-blue-50 border border-blue-200 p-4 mb-6">
                        <h3 class="text-xs font-bold text-blue-800 uppercase tracking-wide mb-2">Payment Processing</h3>
                        <p class="text-xs text-blue-700">Your card payment is being processed. You will receive a confirmation email once the payment is complete.</p>
                    </div>
                @endif

                <div class="bg-gray-50 border border-gray-200 p-4 mb-6">
                    <dl class="space-y-3 text-sm text-gray-600">
                        <div class="flex justify-between pb-3 border-b border-gray-200">
                            <dt class="font-medium text-gray-900">Total Amount</dt>
                            <dd class="font-bold text-lazada-600 text-base">${{ number_format($order->total, 2) }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-xs">Payment Method</dt>
                            <dd class="font-medium text-gray-900 capitalize text-xs">{{ str_replace('_', ' ', $order->payment_method ?? 'Card') }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-xs">Payment Status</dt>
                            <dd>
                                <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-bold uppercase
                                    @if ($order->payment_status->value === 'paid') bg-green-100 text-green-800
                                    @elseif ($order->payment_status->value === 'failed') bg-red-100 text-red-800
                                    @else bg-yellow-100 text-yellow-800
                                    @endif">
                                    {{ $order->payment_status->value }}
                                </span>
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-xs">Order Status</dt>
                            <dd class="font-medium text-gray-900 capitalize text-xs">{{ $order->status->value }}</dd>
                        </div>
                    </dl>
                </div>

                @if ($order->payment_method === 'transfer')
                    <div class="bg-yellow-50 border border-yellow-200 p-4 mb-6">
                        <h3 class="text-xs font-bold text-yellow-800 uppercase tracking-wide mb-2">Bank Transfer Instructions</h3>
                        <p class="text-xs text-yellow-700 mb-3">Please complete your payment within 24 hours to avoid order cancellation.</p>
                        <dl class="space-y-2 text-xs">
                            <div class="flex justify-between">
                                <dt class="text-yellow-700">Bank Name</dt>
                                <dd class="font-medium text-yellow-900">{{ config('app.name') }} Bank</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-yellow-700">Account Number</dt>
                                <dd class="font-medium text-yellow-900 font-mono">1234-5678-9012</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-yellow-700">Account Name</dt>
                                <dd class="font-medium text-yellow-900">{{ config('app.name') }} Marketplace</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-yellow-700">Amount</dt>
                                <dd class="font-bold text-yellow-900">${{ number_format($order->total, 2) }}</dd>
                            </div>
                            @if ($order->payment_reference)
                                <div class="flex justify-between pt-2 border-t border-yellow-200">
                                    <dt class="text-yellow-700">Reference Number</dt>
                                    <dd class="font-bold text-yellow-900 font-mono">{{ $order->payment_reference }}</dd>
                                </div>
                            @endif
                        </dl>
                        <p class="text-[10px] text-yellow-600 mt-3">Please include the reference number in your transfer remarks for faster verification.</p>
                    </div>
                @endif

                <p class="text-xs text-gray-500 text-center mb-6">
                    We've sent a confirmation to your inbox with order details and tracking information.
                </p>

                <div class="flex gap-3">
                    @if (auth()->check() && auth()->user()->role->value === 'customer')
                        <a href="{{ route('customer.orders.show', $order) }}" class="flex-1 h-10 flex items-center justify-center bg-lazada-600 hover:bg-lazada-700 text-white text-sm font-bold transition-colors">
                            View Order
                        </a>
                        <a href="{{ route('storefront.home') }}" class="flex-1 h-10 flex items-center justify-center border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50 transition-colors">
                            Continue Shopping
                        </a>
                    @else
                        <a href="{{ route('storefront.home') }}" class="w-full h-10 flex items-center justify-center bg-lazada-600 hover:bg-lazada-700 text-white text-sm font-bold transition-colors">
                            Back to Home
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
