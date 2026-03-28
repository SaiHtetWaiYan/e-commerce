<x-layouts.app>
    <div class="max-w-2xl mx-auto py-8 px-4">
        <x-ui.card title="File a Dispute — Order #{{ $order->order_number }}">
            <form action="{{ route('customer.disputes.store') }}" method="POST" class="space-y-5">
                @csrf
                <input type="hidden" name="order_id" value="{{ $order->id }}">

                <div>
                    <label for="subject" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Subject</label>
                    <input type="text" name="subject" id="subject" value="{{ old('subject') }}" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary-500 dark:text-white" required placeholder="Brief summary of your issue">
                    @error('subject') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Description</label>
                    <textarea name="description" id="description" rows="6" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary-500 dark:text-white" required placeholder="Please describe your issue in detail...">{{ old('description') }}</textarea>
                    @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit" class="rounded-xl bg-red-600 px-6 py-2.5 text-sm font-bold text-white hover:bg-red-700 transition-colors shadow-sm">Submit Dispute</button>
                    <a href="{{ route('customer.orders.show', $order) }}" class="text-sm font-bold text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">Cancel</a>
                </div>
            </form>
        </x-ui.card>
    </div>
</x-layouts.app>
