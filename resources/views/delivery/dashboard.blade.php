<x-layouts.delivery>
    <div class="mb-8 flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Delivery Dashboard</h1>
            <p class="mt-1 text-sm font-medium text-gray-500 dark:text-gray-400">Track and manage your assigned deliveries.</p>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid gap-4 sm:grid-cols-3 mb-8">
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-6 shadow-sm flex flex-col">
            <div class="flex items-center justify-between mb-4">
                <p class="text-sm font-bold text-gray-500 dark:text-gray-400">Assigned Shipments</p>
                <div class="w-10 h-10 bg-gray-50 dark:bg-gray-800 rounded-xl flex items-center justify-center text-gray-500 dark:text-gray-400 border border-gray-100 dark:border-gray-700 shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                </div>
            </div>
            <p class="text-3xl font-black text-gray-900 dark:text-white">{{ $assignedCount }}</p>
        </div>
        
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-6 shadow-sm flex flex-col relative overflow-hidden">
            <div class="absolute bottom-0 inset-x-0 h-1.5 {{ $inTransitCount > 0 ? 'bg-primary-500 dark:bg-primary-400' : 'bg-gray-200 dark:bg-gray-700' }}"></div>
            <div class="flex items-center justify-between mb-4">
                <p class="text-sm font-bold text-gray-500 dark:text-gray-400">In Transit</p>
                <div class="w-10 h-10 bg-primary-50 dark:bg-primary-900/30 rounded-xl flex items-center justify-center text-primary-600 dark:text-primary-400 border border-primary-100 dark:border-primary-800 shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
            </div>
            <p class="text-3xl font-black text-gray-900 dark:text-white">{{ $inTransitCount }}</p>
            @if($inTransitCount > 0)
                <p class="text-xs text-primary-600 dark:text-primary-400 font-bold mt-1 uppercase tracking-wider">Currently out for delivery</p>
            @endif
        </div>
        
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-6 shadow-sm flex flex-col relative overflow-hidden">
            <div class="absolute bottom-0 inset-x-0 h-1.5 bg-emerald-500 dark:bg-emerald-400"></div>
            <div class="flex items-center justify-between mb-4">
                <p class="text-sm font-bold text-gray-500 dark:text-gray-400">Successfully Delivered</p>
                <div class="w-10 h-10 bg-emerald-50 dark:bg-emerald-900/30 rounded-xl flex items-center justify-center text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-800 shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
            </div>
            <p class="text-3xl font-black text-gray-900 dark:text-white">{{ $deliveredCount }}</p>
        </div>
    </div>

    <!-- Recent Shipments -->
    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-800 flex justify-between items-center bg-gray-50/50 dark:bg-gray-800/50">
            <h2 class="text-lg font-black text-gray-900 dark:text-white tracking-tight">Latest Shipments</h2>
            <a href="{{ route('delivery.shipments.index') }}" class="text-sm font-bold text-primary-600 dark:text-primary-400 hover:text-primary-800 dark:hover:text-primary-300 transition-colors">View All</a>
        </div>
        
        <div class="divide-y divide-gray-200 dark:divide-gray-800">
            @forelse ($shipments as $shipment)
                <div class="p-4 sm:p-6 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex flex-col">
                        <p class="font-black text-gray-900 dark:text-white text-base mb-1">
                            {{ $shipment->tracking_number ?? 'Shipment #'.$shipment->id }}
                        </p>
                        <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Updated {{ $shipment->updated_at->diffForHumans() }}</p>
                    </div>
                    
                    <div class="flex items-center justify-between sm:justify-end w-full sm:w-auto gap-4">
                        @php
                            $statusColors = [
                                'pending' => 'bg-gray-100 dark:bg-gray-800/50 text-gray-800 dark:text-gray-300 border-gray-200 dark:border-gray-700',
                                'assigned' => 'bg-blue-50 dark:bg-blue-900/20 text-blue-800 dark:text-blue-400 border-blue-200 dark:border-blue-800',
                                'picked_up' => 'bg-purple-50 dark:bg-purple-900/20 text-purple-800 dark:text-purple-400 border-purple-200 dark:border-purple-800',
                                'in_transit' => 'bg-primary-50 dark:bg-primary-900/20 text-primary-800 dark:text-primary-400 border-primary-200 dark:border-primary-800',
                                'out_for_delivery' => 'bg-orange-50 dark:bg-orange-900/20 text-orange-800 dark:text-orange-400 border-orange-200 dark:border-orange-800',
                                'delivered' => 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-800 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800',
                                'failed_delivery' => 'bg-red-50 dark:bg-red-900/20 text-red-800 dark:text-red-400 border-red-200 dark:border-red-800',
                            ];
                            $colorClass = $statusColors[$shipment->status->value] ?? 'bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-gray-300 border-gray-200 dark:border-gray-700';
                        @endphp
                        <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold uppercase tracking-wide border shadow-sm {{ $colorClass }}">
                            {{ str_replace('_', ' ', $shipment->status->value) }}
                        </span>
                        
                        <a href="{{ route('delivery.shipments.show', $shipment) }}" class="inline-flex items-center text-sm font-bold text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600 transition-all shadow-sm">
                            Manage
                        </a>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center">
                    <div class="mx-auto w-16 h-16 bg-gray-50 dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-inner rounded-full flex items-center justify-center mb-4 text-gray-400 dark:text-gray-500">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    </div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white mb-1">No assigned shipments</h3>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">When orders are assigned to you for delivery, they will appear here.</p>
                </div>
            @endforelse
        </div>
    </div>
</x-layouts.delivery>
