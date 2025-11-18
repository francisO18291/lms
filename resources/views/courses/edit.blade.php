@extends('layouts.app')

@section('title', 'Edit Course')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Edit Course</h1>
        <p class="text-gray-600 mt-2">Update your course details</p>
    </div>

    <form action="{{ route('courses.update', $course) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-lg shadow p-6">
        @csrf
        @method('PUT')

        <!-- Title -->
        <div class="mb-6">
            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Course Title *</label>
            <input type="text" name="title" id="title" value="{{ old('title', $course->title) }}" required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('title') border-red-500 @enderror">
            @error('title')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Category -->
        <div class="mb-6">
            <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
            <select name="category_id" id="category_id" required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('category_id') border-red-500 @enderror">
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id', $course->category_id) == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            @error('category_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Description -->
        <div class="mb-6">
            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description *</label>
            <textarea name="description" id="description" rows="5" required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('description') border-red-500 @enderror">{{ old('description', $course->description) }}</textarea>
            @error('description')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Requirements -->
        <div class="mb-6">
            <label for="requirements" class="block text-sm font-medium text-gray-700 mb-2">Requirements</label>
            <textarea name="requirements" id="requirements" rows="3"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('requirements') border-red-500 @enderror">{{ old('requirements', $course->requirements) }}</textarea>
            @error('requirements')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Learning Outcomes -->
        <div class="mb-6">
            <label for="learning_outcomes" class="block text-sm font-medium text-gray-700 mb-2">Learning Outcomes</label>
            <textarea name="learning_outcomes" id="learning_outcomes" rows="4"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('learning_outcomes') border-red-500 @enderror">{{ old('learning_outcomes', $course->learning_outcomes) }}</textarea>
            @error('learning_outcomes')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Price, Level, Status -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div>
                <label for="price" class="block text-sm font-medium text-gray-700 mb-2">Price (USD) *</label>
                <input type="number" name="price" id="price" value="{{ old('price', $course->price) }}" min="0" step="0.01" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('price') border-red-500 @enderror">
                @error('price')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="level" class="block text-sm font-medium text-gray-700 mb-2">Level *</label>
                <select name="level" id="level" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('level') border-red-500 @enderror">
                    <option value="beginner" {{ old('level', $course->level) == 'beginner' ? 'selected' : '' }}>Beginner</option>
                    <option value="intermediate" {{ old('level', $course->level) == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                    <option value="advanced" {{ old('level', $course->level) == 'advanced' ? 'selected' : '' }}>Advanced</option>
                </select>
                @error('level')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                <select name="status" id="status" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('status') border-red-500 @enderror">
                    <option value="draft" {{ old('status', $course->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="published" {{ old('status', $course->status) == 'published' ? 'selected' : '' }}>Published</option>
                    <option value="archived" {{ old('status', $course->status) == 'archived' ? 'selected' : '' }}>Archived</option>
                </select>
                @error('status')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Duration -->
        <div class="mb-6">
            <label for="duration_hours" class="block text-sm font-medium text-gray-700 mb-2">Duration (Hours)</label>
            <input type="number" name="duration_hours" id="duration_hours" value="{{ old('duration_hours', $course->duration_hours) }}" min="1"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('duration_hours') border-red-500 @enderror">
            @error('duration_hours')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Current Thumbnail -->
        @if($course->thumbnail)
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Current Thumbnail</label>
                <img src="{{ Storage::url($course->thumbnail) }}" alt="Current thumbnail" class="h-32 rounded-lg">
            </div>
        @endif

        <!-- New Thumbnail -->
        <div class="mb-6">
            <label for="thumbnail" class="block text-sm font-medium text-gray-700 mb-2">Update Thumbnail</label>
            <input type="file" name="thumbnail" id="thumbnail" accept="image/*"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('thumbnail') border-red-500 @enderror">
            @error('thumbnail')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Featured -->
        @if(auth()->user()->isAdmin())
            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $course->is_featured) ? 'checked' : '' }}
                        class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="ml-2 text-sm text-gray-700">Mark as featured course</span>
                </label>
            </div>
        @endif

        <!-- Buttons -->
        <div class="flex items-center justify-between pt-6 border-t">
            <button type="button" onclick="confirmDelete()" class="px-6 py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700">
                Delete Course
            </button>
            <div class="flex space-x-4">
                <a href="{{ route('courses.show', $course) }}" class="px-6 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700">
                    Update Course
                </button>
            </div>
        </div>
    </form>

    <!-- Delete Form -->
    <form id="delete-form" action="{{ route('courses.destroy', $course) }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>
</div>

@push('scripts')
<script>
function confirmDelete() {
    if (confirm('Are you sure you want to delete this course? This action cannot be undone.')) {
        document.getElementById('delete-form').submit();
    }
}
</script>
@endpush
@endsection