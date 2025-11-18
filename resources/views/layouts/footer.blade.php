<footer class="bg-white border-t border-gray-200 mt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <!-- About -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ config('app.name', 'LMS') }}</h3>
                <p class="text-gray-600 text-sm">Empowering learners worldwide with quality online education.</p>
            </div>

            <!-- Quick Links -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Links</h3>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('courses.index') }}" class="text-gray-600 hover:text-indigo-600">Browse Courses</a></li>
                    <li><a href="{{ route('categories.index') }}" class="text-gray-600 hover:text-indigo-600">Categories</a></li>
                    @auth
                        <li><a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-indigo-600">Dashboard</a></li>
                    @endauth
                </ul>
            </div>

            <!-- Support -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Support</h3>
                <ul class="space-y-2 text-sm">
                    <li><a href="#" class="text-gray-600 hover:text-indigo-600">Help Center</a></li>
                    <li><a href="{{route('contact')}}" class="text-gray-600 hover:text-indigo-600">Contact Us</a></li>
                    <li><a href="#" class="text-gray-600 hover:text-indigo-600">FAQs</a></li>
                </ul>
            </div>

            <!-- Legal -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Legal</h3>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('privacy.policy') }}" class="text-gray-600 hover:text-indigo-600">Privacy Policy</a></li>
                    <li><a href="{{ route('terms.service') }}" class="text-gray-600 hover:text-indigo-600">Terms of Service</a></li>
                    <li><a href="{{ route('cookie.policy') }}" class="text-gray-600 hover:text-indigo-600">Cookie Policy</a></li>
                </ul>
            </div>
        </div>

        <div class="border-t border-gray-200 mt-8 pt-8 text-center">
            <p class="text-sm text-gray-600">&copy; {{ date('Y') }} {{ config('app.name', 'LMS') }}. All rights reserved.</p>
        </div>
    </div>
</footer>