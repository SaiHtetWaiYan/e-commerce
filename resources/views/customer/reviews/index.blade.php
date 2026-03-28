<x-layouts.customer>
    <x-ui.card title="My Reviews">
        <div class="space-y-4">
            @forelse ($reviews as $review)
                <div x-data="{ editing: false }" class="rounded-md border border-gray-200 dark:border-gray-700 p-4">
                    <div x-show="!editing" class="flex items-start justify-between">
                        <div>
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('storefront.products.show', $review->product->slug) }}" class="font-medium text-indigo-600 dark:text-indigo-400 hover:underline">
                                    {{ $review->product->name }}
                                </a>
                                <span class="text-xs text-gray-500 dark:text-gray-400">• {{ $review->created_at->diffForHumans() }}</span>
                            </div>
                            
                            <div class="mt-1 flex items-center">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="h-4 w-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                @endfor
                            </div>

                            @if($review->comment)
                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">{{ $review->comment }}</p>
                            @endif

                            @if ($review->reviewImages->isNotEmpty())
                                <div class="mt-3 grid grid-cols-2 gap-3 sm:grid-cols-3">
                                    @foreach ($review->reviewImages as $reviewImage)
                                        @if ($reviewImage->media_type === 'video')
                                            <video controls class="h-28 w-full rounded-xl border border-gray-200 object-cover dark:border-gray-700">
                                                <source src="{{ Storage::url($reviewImage->file_path) }}">
                                            </video>
                                        @else
                                            <img src="{{ Storage::url($reviewImage->file_path) }}" alt="Review media" class="h-28 w-full rounded-xl border border-gray-200 object-cover dark:border-gray-700">
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        <div class="flex space-x-2 ml-4">
                            <button @click="editing = true" class="text-sm text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">Edit</button>
                            <form action="{{ route('customer.reviews.destroy', $review) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this review?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">Delete</button>
                            </form>
                        </div>
                    </div>

                    <!-- Edit Form -->
                    <form x-show="editing" x-cloak action="{{ route('customer.reviews.update', $review) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        @method('PUT')
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Rating</label>
                            <select name="rating" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-800 dark:border-gray-700 dark:text-white" required>
                                @for($i = 5; $i >= 1; $i--)
                                    <option value="{{ $i }}" {{ $review->rating == $i ? 'selected' : '' }}>{{ $i }} Stars</option>
                                @endfor
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Comment</label>
                            <textarea name="comment" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-800 dark:border-gray-700 dark:text-white">{{ $review->comment }}</textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Replace Photos or Videos</label>
                            <input type="file" name="media[]" accept="image/*,video/mp4,video/quicktime,video/webm" multiple class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm dark:bg-gray-800 dark:border-gray-700 dark:text-white">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Leave empty to keep the current media.</p>
                        </div>

                        <div class="flex items-center space-x-3">
                            <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Save Changes</button>
                            <button type="button" @click="editing = false" class="text-sm text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white">Cancel</button>
                        </div>
                    </form>
                </div>
            @empty
                <div class="text-center py-8">
                    <p class="text-gray-500 dark:text-gray-400 mb-2">You haven't submitted any reviews yet.</p>
                    <a href="{{ route('customer.orders.index') }}" class="text-sm text-indigo-600 hover:text-indigo-500 font-medium">View your orders to leave a review</a>
                </div>
            @endforelse
        </div>

        <div class="mt-6">{{ $reviews->links() }}</div>
    </x-ui.card>
</x-layouts.customer>
