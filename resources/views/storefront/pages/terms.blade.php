<x-layouts.app title="Terms of Service">
    <div class="max-w-[800px] mx-auto px-4 py-10">
        <nav class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-8 font-medium">
            <a href="{{ route('storefront.home') }}" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors">Home</a>
            <svg class="w-4 h-4 text-gray-300 dark:text-gray-600 mx-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-gray-900 dark:text-white">Terms of Service</span>
        </nav>

        <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl shadow-sm p-8 md:p-12">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Terms of Service</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-8">Last updated: {{ now()->format('F j, Y') }}</p>

            <div class="prose prose-gray dark:prose-invert max-w-none space-y-6 text-sm leading-relaxed text-gray-600 dark:text-gray-300">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">1. Acceptance of Terms</h2>
                <p>By accessing and using {{ config('app.name') }}, you agree to be bound by these Terms of Service. If you do not agree, please do not use our platform.</p>

                <h2 class="text-lg font-bold text-gray-900 dark:text-white">2. Use of the Platform</h2>
                <p>You must be at least 18 years old to use this service. You are responsible for maintaining the confidentiality of your account credentials and for all activities under your account.</p>

                <h2 class="text-lg font-bold text-gray-900 dark:text-white">3. Marketplace</h2>
                <p>{{ config('app.name') }} is a multi-vendor marketplace. Products are sold by independent vendors. We facilitate the transaction but each vendor is responsible for the accuracy of product listings, fulfillment, and customer service for their products.</p>

                <h2 class="text-lg font-bold text-gray-900 dark:text-white">4. Orders & Payments</h2>
                <p>All prices are displayed in US dollars unless stated otherwise. We reserve the right to refuse or cancel orders at our discretion. Payment must be made at the time of purchase.</p>

                <h2 class="text-lg font-bold text-gray-900 dark:text-white">5. Returns & Refunds</h2>
                <p>Return requests must be submitted within 7 days of delivery. Refunds are processed after the return is approved by the vendor or our admin team. Shipping costs for returns may apply.</p>

                <h2 class="text-lg font-bold text-gray-900 dark:text-white">6. Vendor Responsibilities</h2>
                <p>Vendors must provide accurate product descriptions and images. Vendors are responsible for timely order fulfillment and must comply with all applicable laws and regulations.</p>

                <h2 class="text-lg font-bold text-gray-900 dark:text-white">7. Intellectual Property</h2>
                <p>All content on this platform, including logos, designs, and text, is the property of {{ config('app.name') }} or its vendors. Unauthorized reproduction is prohibited.</p>

                <h2 class="text-lg font-bold text-gray-900 dark:text-white">8. Limitation of Liability</h2>
                <p>{{ config('app.name') }} is not liable for any indirect, incidental, or consequential damages arising from your use of the platform. Our liability is limited to the amount you paid for the relevant transaction.</p>

                <h2 class="text-lg font-bold text-gray-900 dark:text-white">9. Modifications</h2>
                <p>We reserve the right to modify these terms at any time. Changes take effect immediately upon posting. Continued use of the platform constitutes acceptance of the updated terms.</p>

                <h2 class="text-lg font-bold text-gray-900 dark:text-white">10. Contact</h2>
                <p>If you have questions about these Terms, please contact us through our Help Centre.</p>
            </div>
        </div>
    </div>
</x-layouts.app>
