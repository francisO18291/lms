@extends('layouts.app')

@section('title', 'Student Course Progress')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <a href="{{ route('students.show', $user) }}" class="text-indigo-600 hover:text-indigo-700 text-sm font-medium">
            ← Back to {{ $user->name }}'s Profile
        </a>
        <h1 class="text-3xl font-bold text-gray-900 mt-2">Course Progress: {{ $enrollment->course->title }}</h1>
    </div>

    <!-- Progress Overview -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <p class="text-sm text-gray-600 mb-1">Overall Progress</p>
                <div class="flex items-center">
                    <div class="flex-1 bg-gray-200 rounded-full h-4 mr-4">
                        <div class="bg-indigo-600 h-4 rounded-full" style="width: {{ $enrollment->progress }}%"></div>
                    </div>
                    <span class="text-2xl font-bold text-gray-900">{{ $enrollment->progress }}%</span>
                </div>
            </div>
            <div>
                <p class="text-sm text-gray-600 mb-1">Enrolled Date</p>
                <p class="text-lg font-semibold text-gray-900">{{ $enrollment->created_at->format('M d, Y') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 mb-1">Status</p>
                @if($enrollment->isCompleted())
                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">
                        ✓ Completed on {{ $enrollment->completed_at->format('M d, Y') }}
                    </span>
                @elseif($enrollment->isInProgress())
                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800">In Progress</span>
                @else
                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-gray-100 text-gray-800">Not Started</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Course Content with Progress -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-900">Course Content & Progress</h2>
        </div>

        <div class="divide-y divide-gray-200">
            @foreach($enrollment->course->sections as $section)
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">{{ $section->title }}</h3>
                        <span class="text-sm text-gray-500">
                            {{ $section->lessons->whereIn('id', $lessonProgress->pluck('lesson_id'))->where('is_completed', true)->count() }} / {{ $section->lessons->count() }} lessons completed
                        </span>
                    </div>

                    <div class="space-y-2">
                        @foreach($section->lessons as $lesson)
                            @php
                                $progress = $lessonProgress->get($lesson->id);
                                $isCompleted = $progress && $progress->is_completed;
                            @endphp
                            
                            <div class="flex items-center justify-between p-4 rounded-lg {{ $isCompleted ? 'bg-green-50 border border-green-200' : 'bg-gray-50' }}">
                                <div class="flex items-center space-x-3 flex-1">
                                    @if($isCompleted)
                                        <svg class="w-6 h-6 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                    @else
                                        <svg class="w-6 h-6 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <circle cx="12" cy="12" r="10" stroke-width="2"></circle>
                                        </svg>
                                    @endif
                                    
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-2">
                                            @if($lesson->type === 'video')
                                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                            @endif
                                            <span class="text-sm font-medium {{ $isCompleted ? 'text-green-900' : 'text-gray-700' }}">
                                                {{ $lesson->title }}
                                            </span>
                                        </div>
                                        @if($lesson->duration_minutes)
                                            <span class="text-xs text-gray-500">{{ $lesson->formattedDuration() }}</span>
                                        @endif
                                    </div>
                                </div>

                                @if($isCompleted && $progress->completed_at)
                                    <span class="text-xs text-gray-500">
                                        Completed {{ $progress->completed_at->format('M d, Y') }}
                                    </span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection