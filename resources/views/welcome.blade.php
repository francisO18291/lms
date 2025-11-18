@extends('layouts.app')

@section('title', 'Welcome')

@section('content')
<div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
        <div class="text-center">
            <h1 class="text-5xl font-bold mb-6">Learn Anything, Anytime, Anywhere</h1>
            <p class="text-xl mb-8 text-indigo-100">Join thousands of students learning from expert instructors</p>
            <div class="flex justify-center space-x-4">
                @guest
                    <a href="{{ route('register') }}" class="px-8 py-3 bg-white text-indigo-600 font-semibold rounded-lg hover:bg-gray-100 transition">
                        Get Started
                    </a>
                    <a href="{{ route('courses.index') }}" class="px-8 py-3 bg-transparent border-2 border-white text-white font-semibold rounded-lg hover:bg-white hover:text-indigo-600 transition">
                        Browse Courses
                    </a>
                @else
                    <a href="{{ route('dashboard') }}" class="px-8 py-3 bg-white text-indigo-600 font-semibold rounded-lg hover:bg-gray-100 transition">
                        Go to Dashboard
                    </a>
                    <a href="{{ route('courses.index') }}" class="px-8 py-3 bg-transparent border-2 border-white text-white font-semibold rounded-lg hover:bg-white hover:text-indigo-600 transition">
                        Browse Courses
                    </a>
                @endguest
            </div>
        </div>
    </div>
</div>

<!-- Stats Section -->
<div class="bg-white py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 text-center">
            <div>
                <div class="text-4xl font-bold text-indigo-600 mb-2">{{ \App\Models\Course::published()->count() }}</div>
                <div class="text-gray-600">Courses</div>
            </div>
            <div>
                <div class="text-4xl font-bold text-indigo-600 mb-2">{{ \App\Models\User::where('role_id', 3)->count() }}</div>
                <div class="text-gray-600">Students</div>
            </div>
            <div>
                <div class="text-4xl font-bold text-indigo-600 mb-2">{{ \App\Models\User::where('role_id', 2)->count() }}</div>
                <div class="text-gray-600">Instructors</div>
            </div>
            <div>
                <div class="text-4xl font-bold text-indigo-600 mb-2">{{ \App\Models\Category::count() }}</div>
                <div class="text-gray-600">Categories</div>
            </div>
        </div>
    </div>
</div>

<!-- Featured Courses -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="flex justify-between items-center mb-8">
        <h2 class="text-3xl font-bold text-gray-900">Featured Courses</h2>
        <a href="{{ route('courses.index') }}" class="text-indigo-600 hover:text-indigo-700 font-medium">
            View All →
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach(\App\Models\Course::published()->featured()->take(3)->get() as $course)
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                @if($course->thumbnail)
                    <img src="{{ Storage::url($course->thumbnail) }}" alt="{{ $course->title }}" class="w-full h-48 object-cover">
                @else
                    <div class="w-full h-48 bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                        <span class="text-white text-4xl font-bold">{{ substr($course->title, 0, 1) }}</span>
                    </div>
                @endif
                
                <div class="p-6">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-semibold text-indigo-600 uppercase">{{ $course->category->name }}</span>
                        <span class="text-xs text-gray-500">{{ ucfirst($course->level) }}</span>
                    </div>
                    
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $course->title }}</h3>
                    <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ Str::limit($course->description, 100) }}</p>
                    
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <span class="text-2xl font-bold text-gray-900">${{ number_format($course->price, 2) }}</span>
                        </div>
                        <a href="{{ route('courses.show', $course) }}" class="text-indigo-600 hover:text-indigo-700 font-medium text-sm">
                            Learn More →
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- Categories Section -->
<div class="bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-gray-900 mb-8 text-center">Explore Categories</h2>
        
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            @foreach(\App\Models\Category::take(6)->get() as $category)
                <a href="{{ route('categories.show', $category) }}" class="bg-white p-6 rounded-lg text-center hover:shadow-md transition">
                    <div class="text-4xl mb-3">📚</div>
                    <h3 class="font-semibold text-gray-900 mb-1">{{ $category->name }}</h3>
                    <p class="text-sm text-gray-500">{{ $category->courses->count() }} courses</p>
                </a>
            @endforeach
        </div>
    </div>
</div>

<!-- CTA Section -->
<div class="bg-indigo-600 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl font-bold mb-4">Ready to Start Learning?</h2>
        <p class="text-xl mb-8 text-indigo-100">Join our community and unlock your potential</p>
        @guest
            <a href="{{ route('register') }}" class="inline-block px-8 py-3 bg-white text-indigo-600 font-semibold rounded-lg hover:bg-gray-100 transition">
                Sign Up Now
            </a>
        @else
            <a href="{{ route('courses.index') }}" class="inline-block px-8 py-3 bg-white text-indigo-600 font-semibold rounded-lg hover:bg-gray-100 transition">
                Explore Courses
            </a>
        @endguest
    </div>
</div>
@endsection