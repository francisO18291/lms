@extends('layouts.app')

@section('title', 'Teachers')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Teachers</h1>
        <a href="{{ route('teachers.create') }}" class="px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700">
            Add Teacher
        </a>
    </div>

    <!-- Search -->
    <div class="mb-6">
        <form action="{{ route('teachers.index') }}" method="GET" class="flex gap-4">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search teachers by name or email..." 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700">
                Search
            </button>
            @if(request('search'))
                <a href="{{ route('teachers.index') }}" class="px-6 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50">
                    Clear
                </a>
            @endif
        </form>
    </div>

    <!-- Teachers Grid -->
    @if($teachers->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            @foreach($teachers as $teacher)
                <div class="bg-white rounded-lg shadow hover:shadow-lg transition p-6">
                    <div class="flex items-center space-x-4 mb-4">
                        @if($teacher->avatar)
                            <img src="{{ Storage::url($teacher->avatar) }}" alt="{{ $teacher->name }}" class="h-16 w-16 rounded-full object-cover">
                        @else
                            <div class="h-16 w-16 rounded-full bg-indigo-600 flex items-center justify-center text-white text-2xl font-bold">
                                {{ substr($teacher->name, 0, 1) }}
                            </div>
                        @endif
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900">{{ $teacher->name }}</h3>
                            <p class="text-sm text-gray-500">{{ $teacher->email }}</p>
                        </div>
                    </div>

                    @if($teacher->bio)
                        <p class="text-sm text-gray-600 mb-4 line-clamp-3">{{ $teacher->bio }}</p>
                    @endif

                    <div class="flex items-center justify-between py-3 border-t">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-indigo-600">{{ $teacher->taught_courses_count }}</div>
                            <div class="text-xs text-gray-500">Courses</div>
                        </div>
                        <div class="flex space-x-2">
                            <a href="{{ route('teachers.show', $teacher) }}" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
                                View Profile
                            </a>
                            <a href="{{ route('teachers.edit', $teacher) }}" class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50">
                                Edit
                            </a>
                        </div>
                    </div>

                    <div class="mt-3 pt-3 border-t">
                        @if($teacher->is_active)
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Inactive</span>
                        @endif
                        <span class="text-xs text-gray-500 ml-2">Joined {{ $teacher->created_at->format('M Y') }}</span>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $teachers->links() }}
        </div>
    @else
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">No teachers found</h3>
            <p class="mt-2 text-sm text-gray-500">{{ request('search') ? 'Try adjusting your search.' : 'No teachers registered yet.' }}</p>
            <a href="{{ route('teachers.create') }}" class="mt-4 inline-block px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700">
                Add First Teacher
            </a>
        </div>
    @endif
</div>
@endsection