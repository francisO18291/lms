@extends('layouts.app')

@section('title', 'Add Lesson')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <a href="{{ route('courses.show', $section->course) }}" class="text-indigo-600 hover:text-indigo-700 text-sm font-medium">
            ← Back to {{ $section->course->title }}
        </a>
        <h1 class="text-3xl font-bold text-gray-900 mt-2">Add Lesson to "{{ $section->title }}"</h1>
    </div>

    <form action="{{ route('lessons.store', $section) }}" method="POST" class="bg-white rounded-lg shadow p-6">
        @csrf

        <!-- Lesson Title -->
        <div class="mb-6">
            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Lesson Title *</label>
            <input type="text" name="title" id="title" value="{{ old('title') }}" required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('title') border-red-500 @enderror"
                placeholder="e.g., Installing Laravel">
            @error('title')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Lesson Type -->
        <div class="mb-6">
            <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Lesson Type *</label>
            <select name="type" id="type" required onchange="toggleVideoUrl()"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('type') border-red-500 @enderror">
                <option value="video" {{ old('type') == 'video' ? 'selected' : '' }}>Video</option>
                <option value="text" {{ old('type') == 'text' ? 'selected' : '' }}>Text</option>
                <option value="quiz" {{ old('type') == 'quiz' ? 'selected' : '' }}>Quiz</option>
                <option value="assignment" {{ old('type') == 'assignment' ? 'selected' : '' }}>Assignment</option>
            </select>
            @error('type')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Video URL (conditional) -->
        <div id="video-url-field" class="mb-6">
            <label for="video_url" class="block text-sm font-medium text-gray-700 mb-2">Video URL *</label>
            <input type="url" name="video_url" id="video_url" value="{{ old('video_url') }}"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('video_url') border-red-500 @enderror"
                placeholder="https://www.youtube.com/embed/...">
            <p class="mt-1 text-sm text-gray-500">Use YouTube embed URL or direct video link</p>
            @error('video_url')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Content -->
        <div class="mb-6">
            <label for="content" class="block text-sm font-medium text-gray-700 mb-2">Lesson Content</label>
            <textarea name="content" id="content" rows="8"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('content') border-red-500 @enderror"
                placeholder="Add lesson notes, instructions, or text content...">{{ old('content') }}</textarea>
            @error('content')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Duration and Order -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label for="duration_minutes" class="block text-sm font-medium text-gray-700 mb-2">Duration (minutes)</label>
                <input type="number" name="duration_minutes" id="duration_minutes" value="{{ old('duration_minutes') }}" min="1"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('duration_minutes') border-red-500 @enderror">
                @error('duration_minutes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="order" class="block text-sm font-medium text-gray-700 mb-2">Order *</label>
                <input type="number" name="order" id="order" value="{{ old('order', $section->lessons()->count()) }}" min="0" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('order') border-red-500 @enderror">
                @error('order')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Preview Checkbox -->
        <div class="mb-6">
            <label class="flex items-center">
                <input type="checkbox" name="is_preview" value="1" {{ old('is_preview') ? 'checked' : '' }}
                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                <span class="ml-2 text-sm text-gray-700">Allow preview without enrollment</span>
            </label>
        </div>

        <!-- Buttons -->
        <div class="flex items-center justify-end space-x-4 pt-6 border-t">
            <a href="{{ route('courses.show', $section->course) }}" class="px-6 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700">
                Create Lesson
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function toggleVideoUrl() {
    const type = document.getElementById('type').value;
    const videoUrlField = document.getElementById('video-url-field');
    const videoUrlInput = document.getElementById('video_url');
    
    if (type === 'video') {
        videoUrlField.style.display = 'block';
        videoUrlInput.required = true;
    } else {
        videoUrlField.style.display = 'none';
        videoUrlInput.required = false;
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', toggleVideoUrl);
</script>
@endpush
@endsection