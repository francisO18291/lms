@extends('layouts.app')

@section('title', 'Edit Lesson')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <a href="{{ route('courses.show', $lesson->section->course) }}" class="text-indigo-600 hover:text-indigo-700 text-sm font-medium">
            ← Back to {{ $lesson->section->course->title }}
        </a>
        <h1 class="text-3xl font-bold text-gray-900 mt-2">Edit Lesson</h1>
    </div>

    <form action="{{ route('lessons.update', $lesson) }}" method="POST" class="bg-white rounded-lg shadow p-6">
        @csrf
        @method('PUT')

        <!-- Lesson Title -->
        <div class="mb-6">
            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Lesson Title *</label>
            <input type="text" name="title" id="title" value="{{ old('title', $lesson->title) }}" required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('title') border-red-500 @enderror">
            @error('title')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Lesson Type -->
        <div class="mb-6">
            <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Lesson Type *</label>
            <select name="type" id="type" required onchange="toggleVideoUrl()"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('type') border-red-500 @enderror">
                <option value="video" {{ old('type', $lesson->type) == 'video' ? 'selected' : '' }}>Video</option>
                <option value="text" {{ old('type', $lesson->type) == 'text' ? 'selected' : '' }}>Text</option>
                <option value="quiz" {{ old('type', $lesson->type) == 'quiz' ? 'selected' : '' }}>Quiz</option>
                <option value="assignment" {{ old('type', $lesson->type) == 'assignment' ? 'selected' : '' }}>Assignment</option>
            </select>
            @error('type')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Video URL (conditional) -->
        <div id="video-url-field" class="mb-6">
            <label for="video_url" class="block text-sm font-medium text-gray-700 mb-2">Video URL</label>
            <input type="url" name="video_url" id="video_url" value="{{ old('video_url', $lesson->video_url) }}"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('video_url') border-red-500 @enderror"
                placeholder="https://www.youtube.com/embed/...">
            @error('video_url')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Content -->
        <div class="mb-6">
            <label for="content" class="block text-sm font-medium text-gray-700 mb-2">Lesson Content</label>
            <textarea name="content" id="content" rows="8"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('content') border-red-500 @enderror">{{ old('content', $lesson->content) }}</textarea>
            @error('content')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Duration and Order -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label for="duration_minutes" class="block text-sm font-medium text-gray-700 mb-2">Duration (minutes)</label>
                <input type="number" name="duration_minutes" id="duration_minutes" value="{{ old('duration_minutes', $lesson->duration_minutes) }}" min="1"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('duration_minutes') border-red-500 @enderror">
                @error('duration_minutes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="order" class="block text-sm font-medium text-gray-700 mb-2">Order *</label>
                <input type="number" name="order" id="order" value="{{ old('order', $lesson->order) }}" min="0" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('order') border-red-500 @enderror">
                @error('order')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Preview Checkbox -->
        <div class="mb-6">
            <label class="flex items-center">
                <input type="checkbox" name="is_preview" value="1" {{ old('is_preview', $lesson->is_preview) ? 'checked' : '' }}
                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                <span class="ml-2 text-sm text-gray-700">Allow preview without enrollment</span>
            </label>
        </div>

        <!-- Buttons -->
        <div class="flex items-center justify-between pt-6 border-t">
            <button type="button" onclick="confirmDelete()" class="px-6 py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700">
                Delete Lesson
            </button>
            <div class="flex space-x-4">
                <a href="{{ route('courses.show', $lesson->section->course) }}" class="px-6 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700">
                    Update Lesson
                </button>
            </div>
        </div>
    </form>

    <!-- Delete Form -->
    <form id="delete-form" action="{{ route('lessons.destroy', $lesson) }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
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

function confirmDelete() {
    if (confirm('Are you sure you want to delete this lesson? This action cannot be undone.')) {
        document.getElementById('delete-form').submit();
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', toggleVideoUrl);
</script>
@endpush
@endsection