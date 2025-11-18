@extends('layouts.app')

@section('title', 'My Enrollments')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">My Enrollments</h1>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-600 mb-1">Total Courses</p>
            <p class="text-3xl font-bold text-gray-900">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-600 mb-1">Completed</p>
            <p class="text-3xl font-bold text-green-600">{{ $stats['completed'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-600 mb-1">In Progress</p>
            <p class="text-3xl font-bold text-yellow-600">{{ $stats['in_progress'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-600 mb-1">Not Started</p>
            <p class="text-3xl font-bold text-gray-600">{{ $stats['not_started'] }}</p>
        </div>
    </div>

    <!-- Tabs -->
    <div class="mb-6" x-data="{ tab: 'all' }">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8">
                <button @click="tab = 'all'" :class="tab === 'all' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" 
                    class="py-4 px-1 border-b-2 font-medium text-sm">
                    All Courses ({{ $stats['total'] }})
                </button>
                <button @click="tab = 'in-progress'" :class="tab === 'in-progress' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" 
                    class="py-4 px-1 border-b-2 font-medium text-sm">
                    In Progress ({{ $stats['in_progress'] }})
                </button>
                <button @click="tab = 'completed'" :class="tab === 'completed' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" 
                    class="py-4 px-1 border-b-2 font-medium text-sm">
                    Completed ({{ $stats['completed'] }})
                </button>
            </nav>
        </div>

        <!-- All Courses -->
        <div x-show="tab === 'all'" class="mt-6">
            @if($enrollments->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($enrollments as $enrollment)
                        <div class="bg-white rounded-lg shadow hover:shadow-lg transition">
                            @if($enrollment->course->thumbnail)
                                <img src="{{ Storage::url($enrollment->course->thumbnail) }}" alt="{{ $enrollment->course->title }}" class="w-full h-40 object-cover rounded-t-lg">
                            @else
                                <div class="w-full h-40 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-t-lg flex items-center justify-center">
                                    <span class="text-white text-3xl font-bold">{{ substr($enrollment->course->title, 0, 1) }}</span>
                                </div>
                            @endif
                            
                            <div class="p-6">
                                <span class="text-xs font-semibold text-indigo-600 uppercase">{{ $enrollment->course->category->name }}</span>
                                <h3 class="text-lg font-semibold text-gray-900 mt-2 mb-3">{{ $enrollment->course->title }}</h3>
                                
                                <div class="mb-4">
                                    <div class="flex items-center justify-between text-sm text-gray-600 mb-1">
                                        <span>Progress</span>
                                        <span>{{ $enrollment->progress }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-indigo-600 h-2 rounded-full transition-all" style="width: {{ $enrollment->progress }}%"></div>
                                    </div>
                                </div>

                                @if($enrollment->isCompleted())
                                    <div class="flex space-x-2">
                                        <a href="{{ route('enrollments.certificate', $enrollment) }}" class="flex-1 px-4 py-2 bg-green-600 text-white text-center text-sm font-medium rounded-lg hover:bg-green-700">
                                            Certificate
                                        </a>
                                        <a href="{{ route('courses.show', $enrollment->course) }}" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 text-center text-sm font-medium rounded-lg hover:bg-gray-50">
                                            Review
                                        </a>
                                    </div>
                                @else
                                  <!--make sure that each section has got atleast one lesson-->
                                    <a href="{{ route('courses.lessons.show', [$enrollment->course, $enrollment->course->sections->first()->lessons->first()]) }}" class="block w-full px-4 py-2 bg-indigo-600 text-white text-center text-sm font-medium rounded-lg hover:bg-indigo-700">
                                        Continue Learning
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-lg shadow p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">No enrollments yet</h3>
                    <p class="mt-2 text-sm text-gray-500">Start learning by enrolling in a course.</p>
                    <a href="{{ route('courses.index') }}" class="mt-4 inline-block px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700">
                        Browse Courses
                    </a>
                </div>
            @endif
        </div>

        <!-- In Progress -->
        <div x-show="tab === 'in-progress'" class="mt-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($enrollments->where('progress', '>', 0)->where('progress', '<', 100) as $enrollment)
                    <div class="bg-white rounded-lg shadow hover:shadow-lg transition">
                        @if($enrollment->course->thumbnail)
                            <img src="{{ Storage::url($enrollment->course->thumbnail) }}" alt="{{ $enrollment->course->title }}" class="w-full h-40 object-cover rounded-t-lg">
                        @else
                            <div class="w-full h-40 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-t-lg"></div>
                        @endif
                        
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-3">{{ $enrollment->course->title }}</h3>
                            
                            <div class="mb-4">
                                <div class="flex items-center justify-between text-sm text-gray-600 mb-1">
                                    <span>Progress</span>
                                    <span>{{ $enrollment->progress }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $enrollment->progress }}%"></div>
                                </div>
                            </div>

                            <a href="{{ route('courses.lessons.show', [$enrollment->course, $enrollment->course->sections->first()->lessons->first()]) }}" class="block w-full px-4 py-2 bg-indigo-600 text-white text-center text-sm font-medium rounded-lg hover:bg-indigo-700">
                                Continue Learning
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Completed -->
        <div x-show="tab === 'completed'" class="mt-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($enrollments->where('progress', 100) as $enrollment)
                    <div class="bg-white rounded-lg shadow hover:shadow-lg transition">
                        @if($enrollment->course->thumbnail)
                            <img src="{{ Storage::url($enrollment->course->thumbnail) }}" alt="{{ $enrollment->course->title }}" class="w-full h-40 object-cover rounded-t-lg">
                        @else
                            <div class="w-full h-40 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-t-lg"></div>
                        @endif
                        
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-lg font-semibold text-gray-900">{{ $enrollment->course->title }}</h3>
                                <svg class="w-6 h-6 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            
                            <p class="text-sm text-gray-600 mb-4">Completed on {{ $enrollment->completed_at->format('M d, Y') }}</p>

                            <div class="flex space-x-2">
                                <a href="{{ route('enrollments.certificate', $enrollment) }}" class="flex-1 px-4 py-2 bg-green-600 text-white text-center text-sm font-medium rounded-lg hover:bg-green-700">
                                    Certificate
                                </a>
                                <a href="{{ route('courses.show', $enrollment->course) }}" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 text-center text-sm font-medium rounded-lg hover:bg-gray-50">
                                    Review
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection