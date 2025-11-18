@extends('layouts.app')

@section('title', 'Create Category')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <a href="{{ route('categories.index') }}" class="text-indigo-600 hover:text-indigo-700 text-sm font-medium">
            ← Back to Categories
        </a>
        <h1 class="text-3xl font-bold text-gray-900 mt-2">Create New Category</h1>
    </div>

    <form action="{{ route('categories.store') }}" method="POST" class="bg-white rounded-lg shadow p-6">
        @csrf

        <!-- Name -->
        <div class="mb-6">
            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Category Name *</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('name') border-red-500 @enderror"
                placeholder="e.g., Web Development">
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Description -->
        <div class="mb-6">
            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
            <textarea name="description" id="description" rows="4"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('description') border-red-500 @enderror"
                placeholder="Brief description of this category...">{{ old('description') }}</textarea>
            @error('description')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Icon -->
        <div class="mb-6">
            <label for="icon" class="block text-sm font-medium text-gray-700 mb-2">Icon (optional)</label>
            <input type="text" name="icon" id="icon" value="{{ old('icon') }}"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('icon') border-red-500 @enderror"
                placeholder="e.g., code, chart-bar, mobile">
            <p class="mt-1 text-sm text-gray-500">Icon name for display (e.g., Heroicons or Font Awesome icon names)</p>
            @error('icon')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Buttons -->
        <div class="flex items-center justify-end space-x-4 pt-6 border-t">
            <a href="{{ route('categories.index') }}" class="px-6 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700">
                Create Category
            </button>
        </div>
    </form>
</div>
@endsection