@extends('layouts.app')

@section('title', $lesson->title)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-3">
            <!-- Lesson Video/Content -->
            <div class="bg-white rounded-lg shadow mb-6 overflow-hidden">
                @if($lesson->type === 'video' && $lesson->video_url)
                    <div class="aspect-w-16 aspect-h-9 bg-black">
                        <iframe src="{{ $lesson->video_url }}" frameborder="0" allowfullscreen class="w-full h-full" style="min-height: 500px;"></iframe>
                    </div>
                @else
                    <div class="p-8">
                        <div class="prose max-w-none">
                            {!! nl2br(e($lesson->content)) !!}
                        </div>
                    </div>
                @endif
            </div>

            <!-- Lesson Details -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h1 class="text-3xl font-bold text-gray-900">{{ $lesson->title }}</h1>
                    @if($progress)
                        <button onclick="toggleComplete()" id="complete-btn" 
                            class="px-6 py-2 rounded-lg font-medium transition {{ $progress->is_completed ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-indigo-600 text-white hover:bg-indigo-700' }}">
                            <span id="complete-text">{{ $progress->is_completed ? '✓ Completed' : 'Mark Complete' }}</span>
                        </button>
                    @endif
                </div>

                <div class="flex items-center space-x-6 text-sm text-gray-600 mb-6">
                    <span class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ $lesson->formattedDuration() }}
                    </span>
                    <span class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                        </svg>
                        {{ ucfirst($lesson->type) }}
                    </span>
                </div>

                @if($lesson->content && $lesson->type === 'video')
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Lesson Notes</h3>
                        <div class="prose max-w-none text-gray-600">
                            {!! nl2br(e($lesson->content)) !!}
                        </div>
                    </div>
                @endif
            </div>

            <!-- Navigation -->
            <div class="flex items-center justify-between">
                <a href="{{ route('courses.show', $course) }}" class="px-6 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50">
                    ← Back to Course
                </a>
                @if($nextLesson)
                    <a href="{{ route('courses.lessons.show', [$course, $nextLesson]) }}" class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700">
                        Next Lesson →
                    </a>
                @else
                    <span class="px-6 py-2 bg-gray-300 text-gray-500 font-medium rounded-lg cursor-not-allowed">
                        Course Complete
                    </span>
                @endif
            </div>
        </div>

        <!-- Sidebar - Course Content -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow p-6 sticky top-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Course Content</h3>
                
                <div class="space-y-4 max-h-[600px] overflow-y-auto">
                    @foreach($course->sections as $section)
                        <div>
                            <h4 class="text-sm font-semibold text-gray-900 mb-2">{{ $section->title }}</h4>
                            <div class="space-y-1">
                                @foreach($section->lessons as $sectionLesson)
                                    <a href="{{ route('courses.lessons.show', [$course, $sectionLesson]) }}"
                                        class="block px-3 py-2 rounded text-sm transition {{ $sectionLesson->id === $lesson->id ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-700 hover:bg-gray-50' }}">
                                        <div class="flex items-center justify-between">
                                            <span class="truncate">{{ $sectionLesson->title }}</span>
                                            @auth
                                                @if(auth()->user()->hasCompletedLesson($sectionLesson))
                                                    <svg class="w-4 h-4 text-green-500 flex-shrink-0 ml-2" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                    </svg>
                                                @endif
                                            @endauth
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function toggleComplete() {
    const btn = document.getElementById('complete-btn');
    const text = document.getElementById('complete-text');
    const isCompleted = btn.classList.contains('bg-green-100');
    
    const url = isCompleted 
        ? "{{ route('lessons.incomplete', $lesson) }}"
        : "{{ route('lessons.complete', $lesson) }}";
    
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (isCompleted) {
                btn.classList.remove('bg-green-100', 'text-green-700', 'hover:bg-green-200');
                btn.classList.add('bg-indigo-600', 'text-white', 'hover:bg-indigo-700');
                text.textContent = 'Mark Complete';
            } else {
                btn.classList.remove('bg-indigo-600', 'text-white', 'hover:bg-indigo-700');
                btn.classList.add('bg-green-100', 'text-green-700', 'hover:bg-green-200');
                text.textContent = '✓ Completed';
            }
        }
    })
    .catch(error => console.error('Error:', error));
}
</script>
@endpush
@endsection