@extends('layouts.app')

@section('title', 'Edit Category')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <a href="{{ route('categories.index') }}" class="text-indigo-600 hover:text-indigo-700 text-sm font-medium">
            ← Back to Categories
        </a>
        <h1 class="text-3xl font-bold text-gray-900 mt-2">Edit Category</h1>
    </div>

    <form action="{{ route('categories.update', $category) }}" method="POST" class="bg-white rounded-lg shadow p-6">
        @csrf
        @method('PUT')

        <!-- Name -->
        <div class="mb-6">
            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Category Name *</label>
            <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}" required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('name') border-red-500 @enderror">
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Description -->
        <div class="mb-6">
            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
            <textarea name="description" id="description" rows="4"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('description') border-red-500 @enderror">{{ old('description', $category->description) }}</textarea>
            @error('description')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Icon -->
        <div class="mb-6">
            <label for="icon" class="block text-sm font-medium text-gray-700 mb-2">Icon (optional)</label>
            <input type="text" name="icon" id="icon" value="{{ old('icon', $category->icon) }}"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('icon') border-red-500 @enderror">
            @error('icon')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Course Count Info -->
        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <p class="text-sm text-blue-800">
                This category has <strong>{{ $category->courses()->count() }}</strong> course(s).
            </p>
        </div>

        <!-- Buttons -->
        <div class="flex items-center justify-between pt-6 border-t">
            <button type="button" onclick="confirmDelete()" class="px-6 py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700"
                {{ $category->courses()->count() > 0 ? 'disabled' : '' }}>
                Delete Category
            </button>
            <div class="flex space-x-4">
                <a href="{{ route('categories.index') }}" class="px-6 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700">
                    Update Category
                </button>
            </div>
        </div>
    </form>

    <!-- Delete Form -->
    <form id="delete-form" action="{{ route('categories.destroy', $category) }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>
</div>

@push('scripts')
<script>
function confirmDelete() {
    if (confirm('Are you sure you want to delete this category? This action cannot be undone.')) {
        document.getElementById('delete-form').submit();
    }
}
</script>
@endpush
@endsection