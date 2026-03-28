<x-layouts.app title="Privacy Policy">
    <div class="max-w-[800px] mx-auto px-4 py-10">
        <nav class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-8 font-medium">
            <a href="{{ route('storefront.home') }}" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors">Home</a>
            <svg class="w-4 h-4 text-gray-300 dark:text-gray-600 mx-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-gray-900 dark:text-white">Privacy Policy</span>
        </nav>

        <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl shadow-sm p-8 md:p-12">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Privacy Policy</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-8">Last updated: {{ now()->format('F j, Y') }}</p>

            <div class="prose prose-gray dark:prose-invert max-w-none space-y-6 text-sm leading-relaxed text-gray-600 dark:text-gray-300">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">1. Information We Collect</h2>
                <p>We collect information you provide directly, including your name, email address, shipping address, and payment details when you create an account or place an order.</p>

                <h2 class="text-lg font-bold text-gray-900 dark:text-white">2. How We Use Your Information</h2>
                <p>We use your information to process orders, communicate with you about your account, improve our services, and send promotional content (with your consent).</p>

                <h2 class="text-lg font-bold text-gray-900 dark:text-white">3. Information Sharing</h2>
                <p>We share your shipping details with vendors to fulfill orders. We do not sell your personal information to third parties. We may share data with service providers who assist in operating the platform.</p>

                <h2 class="text-lg font-bold text-gray-900 dark:text-white">4. Data Security</h2>
                <p>We implement industry-standard security measures to protect your personal information, including encrypted data transmission and secure storage. However, no method of transmission over the internet is 100% secure.</p>

                <h2 class="text-lg font-bold text-gray-900 dark:text-white">5. Cookies</h2>
                <p>We use cookies and similar technologies to enhance your experience, remember your preferences, and analyze site traffic. You can control cookie settings through your browser.</p>

                <h2 class="text-lg font-bold text-gray-900 dark:text-white">6. Your Rights</h2>
                <p>You have the right to access, update, or delete your personal information. You can manage your account details through your profile settings or by contacting our support team.</p>

                <h2 class="text-lg font-bold text-gray-900 dark:text-white">7. Data Retention</h2>
                <p>We retain your personal information as long as your account is active or as needed to provide services. We may retain certain information for legal compliance or legitimate business purposes.</p>

                <h2 class="text-lg font-bold text-gray-900 dark:text-white">8. Children's Privacy</h2>
                <p>Our platform is not intended for users under 18 years of age. We do not knowingly collect information from minors.</p>

                <h2 class="text-lg font-bold text-gray-900 dark:text-white">9. Changes to This Policy</h2>
                <p>We may update this Privacy Policy periodically. We will notify you of significant changes via email or through the platform.</p>

                <h2 class="text-lg font-bold text-gray-900 dark:text-white">10. Contact</h2>
                <p>For privacy-related inquiries, please contact us through our Help Centre or email our data protection team.</p>
            </div>
        </div>
    </div>
</x-layouts.app>
