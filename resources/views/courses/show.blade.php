@extends('layouts.app')

@section('title', $course->title)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Course Header -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-lg shadow-lg p-8 mb-8 text-white">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2">
                <div class="flex items-center space-x-2 mb-4">
                    <span class="px-3 py-1 bg-white/20 rounded text-sm font-medium">{{ $course->category->name }}</span>
                    <span class="px-3 py-1 bg-white/20 rounded text-sm font-medium">{{ ucfirst($course->level) }}</span>
                </div>
                
                <h1 class="text-4xl font-bold mb-4">{{ $course->title }}</h1>
                <p class="text-lg text-indigo-100 mb-6">{{ $course->description }}</p>
                
                <div class="flex items-center space-x-6 text-sm">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        {{ $course->teacher->name }}
                    </div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        {{ $course->studentsCount() }} students
                    </div>
                    @if($course->duration_hours)
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ $course->duration_hours }} hours
                        </div>
                    @endif
                </div>
            </div>

            <!-- Enrollment Card -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-lg p-6 text-gray-900">
                    <div class="text-3xl font-bold mb-4">
                        @if($course->price > 0)
                            ${{ number_format($course->price, 2) }}
                        @else
                            <span class="text-green-600">Free</span>
                        @endif
                    </div>

                    @auth
                        @if($isEnrolled)
                            <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                                <p class="text-green-800 font-medium">You're enrolled!</p>
                                <p class="text-sm text-green-600 mt-1">Progress: {{ $enrollment->progress }}%</p>
                            </div>
                            <a href="{{ route('courses.lessons.show', [$course, $course->sections->first()->lessons->first()]) }}" class="block w-full px-6 py-3 bg-indigo-600 text-white text-center font-semibold rounded-lg hover:bg-indigo-700 mb-2">
                                Continue Learning
                            </a>
                        @else
                            <form action="{{ route('enrollments.store', $course) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full px-6 py-3 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 mb-2">
                                    Enroll Now
                                </button>
                            </form>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="block w-full px-6 py-3 bg-indigo-600 text-white text-center font-semibold rounded-lg hover:bg-indigo-700 mb-2">
                            Login to Enroll
                        </a>
                    @endauth

                    @can('update', $course)
                        <div class="mt-4 pt-4 border-t space-y-2">
                            <a href="{{ route('courses.edit', $course) }}" class="block w-full px-4 py-2 bg-gray-100 text-gray-700 text-center font-medium rounded-lg hover:bg-gray-200">
                                Edit Course
                            </a>
                            @if($course->status === 'draft')
                                <form action="{{ route('courses.publish', $course) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700">
                                        Publish Course
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('courses.unpublish', $course) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full px-4 py-2 bg-yellow-600 text-white font-medium rounded-lg hover:bg-yellow-700">
                                        Unpublish Course
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endcan
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-8">
            <!-- What you'll learn -->
            @if($course->learning_outcomes)
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">What you'll learn</h2>
                    <div class="prose prose-sm text-gray-600">
                        {!! nl2br(e($course->learning_outcomes)) !!}
                    </div>
                </div>
            @endif

            <!-- Requirements -->
            @if($course->requirements)
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Requirements</h2>
                    <div class="prose prose-sm text-gray-600">
                        {!! nl2br(e($course->requirements)) !!}
                    </div>
                </div>
            @endif

            <!-- Course Content -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Course Content</h2>
                
                <div class="space-y-4">
                    @foreach($course->sections as $section)
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-900">{{ $section->title }}</h3>
                                <p class="text-sm text-gray-500 mt-1">{{ $section->lessonsCount() }} lessons • {{ $section->totalDuration() }} min</p>
                            </div>
                            
                            <div class="divide-y divide-gray-200">
                                @foreach($section->lessons as $lesson)
                                    <div class="px-6 py-4 hover:bg-gray-50">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-3 flex-1">
                                                @if($lesson->type === 'video')
                                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                @else
                                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                    </svg>
                                                @endif
                                                <span class="text-sm text-gray-700">{{ $lesson->title }}</span>
                                            </div>
                                            <div class="flex items-center space-x-3">
                                                @if($lesson->duration_minutes)
                                                    <span class="text-sm text-gray-500">{{ $lesson->formattedDuration() }}</span>
                                                @endif
                                                @if($lesson->is_preview)
                                                    <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded">Preview</span>
                                                @endif
                                                @can('update', [$lesson, $course])
                                                    <a href="{{ route('lessons.edit', $lesson) }}" class="text-indigo-600 hover:text-indigo-900 text-sm">
                                                        Edit
                                                    </a>
                                                @endcan
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            @can('manageContent', $course)
                                <div class="px-6 py-3 bg-gray-50 border-t">
                                    <a href="{{ route('lessons.create', $section) }}" class="text-indigo-600 hover:text-indigo-700 text-sm font-medium">
                                        + Add Lesson
                                    </a>
                                </div>
                            @endcan
                        </div>
                    @endforeach
                </div>

                @can('manageContent', $course)
                    <div class="mt-6 pt-6 border-t flex items-center justify-between">
                        <a href="{{ route('sections.create', $course) }}" class="text-indigo-600 hover:text-indigo-700 font-medium">
                            + Add Section
                        </a>
                        @if($course->sections->count() > 0)
                            <a href="{{ route('sections.edit', $course->sections->first()) }}" class="text-gray-600 hover:text-gray-700 text-sm">
                                Manage Sections
                            </a>
                        @endif
                    </div>
                @endcan
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <!-- Instructor -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Instructor</h3>
                <div class="flex items-center space-x-4">
                    @if($course->teacher->avatar)
                        <img src="{{ Storage::url($course->teacher->avatar) }}" alt="{{ $course->teacher->name }}" class="w-16 h-16 rounded-full object-cover">
                    @else
                        <div class="w-16 h-16 rounded-full bg-indigo-600 flex items-center justify-center text-white text-2xl font-bold">
                            {{ substr($course->teacher->name, 0, 1) }}
                        </div>
                    @endif
                    <div>
                        <h4 class="font-semibold text-gray-900">{{ $course->teacher->name }}</h4>
                        @if($course->teacher->bio)
                            <p class="text-sm text-gray-600 mt-1">{{ Str::limit($course->teacher->bio, 60) }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection