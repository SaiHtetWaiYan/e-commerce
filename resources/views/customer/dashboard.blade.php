<x-layouts.customer>
    <div class="mb-8">
        <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Welcome back, {{ explode(' ', auth()->user()->name)[0] }}!</h1>
        <p class="mt-2 text-sm font-medium text-gray-500 dark:text-gray-400">Here's an overview of your account activity.</p>
    </div>

    <!-- Quick Stats -->
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 mb-8">
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 p-6 shadow-sm flex items-center justify-between group hover:shadow-md transition-shadow">
            <div>
                <p class="text-sm font-bold text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wider">Total Orders</p>
                <p class="text-4xl font-black text-gray-900 dark:text-white tracking-tight">{{ $recentOrders->count() }}</p>
            </div>
            <div class="w-14 h-14 bg-primary-50 dark:bg-primary-900/20 rounded-2xl flex items-center justify-center text-primary-600 dark:text-primary-400 group-hover:scale-110 transition-transform">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
            </div>
        </div>
        
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 p-6 shadow-sm flex items-center justify-between group hover:shadow-md transition-shadow">
            <div>
                <p class="text-sm font-bold text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wider">Wishlist Items</p>
                <p class="text-4xl font-black text-gray-900 dark:text-white tracking-tight">{{ $wishlistCount }}</p>
            </div>
            <div class="w-14 h-14 bg-accent-50 dark:bg-accent-900/20 rounded-2xl flex items-center justify-center text-accent-600 dark:text-accent-400 group-hover:scale-110 transition-transform">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
            </div>
        </div>
        
        <div class="bg-gradient-to-br from-primary-600 to-primary-800 dark:from-primary-700 dark:to-primary-900 rounded-2xl p-6 shadow-sm sm:col-span-2 lg:col-span-1 text-white flex flex-col justify-center relative overflow-hidden group">
            <!-- Decorative circle -->
            <div class="absolute -right-6 -top-6 w-32 h-32 rounded-full bg-white/10 blur-2xl group-hover:bg-white/20 transition-colors"></div>
            
            <p class="text-primary-100 dark:text-primary-200 text-sm font-bold mb-1.5 uppercase tracking-wider relative z-10 w-fit">Need help?</p>
            <h3 class="text-xl font-black mb-4 relative z-10 tracking-tight">Contact Support</h3>
            <a href="#" class="inline-flex items-center text-sm font-bold text-white hover:text-primary-100 transition-colors relative z-10 w-fit group/btn">
                <span class="border-b-2 border-transparent group-hover/btn:border-primary-100 transition-colors pb-0.5">Get in touch</span>
                <svg class="w-4 h-4 ml-1.5 group-hover/btn:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </a>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800 flex justify-between items-center bg-gray-50/50 dark:bg-gray-800/50">
            <h2 class="text-lg font-black text-gray-900 dark:text-white">Recent Orders</h2>
            <a href="{{ route('customer.orders.index') }}" class="text-sm font-bold text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 transition-colors flex items-center gap-1 group">
                View All
                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
        
        <div class="divide-y divide-gray-100 dark:divide-gray-800">
            @forelse ($recentOrders as $order)
                <div class="p-6 hover:bg-gray-50/80 dark:hover:bg-gray-800/80 transition-colors">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                            <div class="w-14 h-14 rounded-2xl bg-gray-50 dark:bg-gray-800 border border-gray-100 dark:border-gray-700 flex items-center justify-center flex-shrink-0 text-gray-500 dark:text-gray-400">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                            </div>
                            <div>
                                <p class="text-base font-bold text-gray-900 dark:text-white mb-1">Order #{{ $order->order_number }}</p>
                                <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 font-medium whitespace-nowrap">
                                    <span>Placed on {{ $order->created_at->format('M d, Y') }}</span>
                                    <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-600"></span>
                                    <span class="font-bold text-gray-700 dark:text-gray-300">${{ number_format($order->total, 2) }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-4 sm:justify-end mt-2 sm:mt-0">
                            @php
                                $statusColors = [
                                    'pending' => 'bg-yellow-50 dark:bg-yellow-900/20 text-yellow-700 dark:text-yellow-400 border-yellow-200 dark:border-yellow-800',
                                    'processing' => 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 dark:text-blue-400 border-blue-200 dark:border-blue-800',
                                    'shipped' => 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-400 border-primary-200 dark:border-primary-800',
                                    'delivered' => 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800',
                                    'cancelled' => 'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 dark:text-red-400 border-red-200 dark:border-red-800',
                                ];
                                $colorClass = $statusColors[$order->status->value] ?? 'bg-gray-50 dark:bg-gray-800 text-gray-700 dark:text-gray-400 border-gray-200 dark:border-gray-700';
                            @endphp
                            <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold uppercase tracking-wide border shadow-sm {{ $colorClass }}">
                                {{ $order->status->value }}
                            </span>
                            
                            <a href="{{ route('customer.orders.show', $order) }}" class="text-sm font-bold text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600 transition-all shadow-sm whitespace-nowrap">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-16 text-center">
                    <div class="mx-auto w-20 h-20 bg-gray-50 dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-full flex items-center justify-center mb-5 text-gray-400 dark:text-gray-500 shadow-inner">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    </div>
                    <h3 class="text-lg font-black text-gray-900 dark:text-white mb-2">No recent orders</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-6 font-medium max-w-sm mx-auto">When you place an order, it will appear here.</p>
                    <a href="{{ route('storefront.products.index') }}" class="inline-flex items-center text-sm font-bold text-white bg-primary-600 hover:bg-primary-700 px-6 py-2.5 rounded-xl transition-all shadow-sm uppercase tracking-wide">
                        Start shopping 
                    </a>
                </div>
            @endforelse
        </div>
    </div>
</x-layouts.customer>
