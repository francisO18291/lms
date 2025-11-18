@extends('layouts.app')

@section('title', 'Add Section')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <a href="{{ route('courses.show', $course) }}" class="text-indigo-600 hover:text-indigo-700 text-sm font-medium">
            ← Back to {{ $course->title }}
        </a>
        <h1 class="text-3xl font-bold text-gray-900 mt-2">Add New Section</h1>
    </div>

    <form action="{{ route('sections.store', $course) }}" method="POST" class="bg-white rounded-lg shadow p-6">
        @csrf

        <!-- Section Title -->
        <div class="mb-6">
            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Section Title *</label>
            <input type="text" name="title" id="title" value="{{ old('title') }}" required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('title') border-red-500 @enderror"
                placeholder="e.g., Introduction to Laravel">
            @error('title')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Order -->
        <div class="mb-6">
            <label for="order" class="block text-sm font-medium text-gray-700 mb-2">Order *</label>
            <input type="number" name="order" id="order" value="{{ old('order', $course->sections()->count()) }}" min="0" required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('order') border-red-500 @enderror">
            <p class="mt-1 text-sm text-gray-500">Lower numbers appear first</p>
            @error('order')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Buttons -->
        <div class="flex items-center justify-end space-x-4 pt-6 border-t">
            <a href="{{ route('courses.show', $course) }}" class="px-6 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700">
                Create Section
            </button>
        </div>
    </form>
</div>
@endsection