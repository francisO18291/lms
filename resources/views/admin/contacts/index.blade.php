@extends('layouts.dashboard')

@section('title', 'Contact Messages')
@section('page-title', 'Contact Messages')

@section('content')
<div class="mb-4">
    <a href="{{ route('dashboard') }}" class="text-indigo-600 hover:text-indigo-700">
        ← Back to dashboard
    </a>
</div>
<div class="mb-6">
    <div class="flex justify-between items-center mb-4">
        <div class="flex-1 max-w-md">
            <form action="{{ route('admin.contacts.index') }}" method="GET" class="flex gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search messages..." class="flex-1 px-4 py-2 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <select name="status" class="px-4 py-2 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500" style="padding-right: 30px;">
                    <option value="">All Status</option>
                    <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>New</option>
                    <option value="read" {{ request('status') == 'read' ? 'selected' : '' }}>Read</option>
                    <option value="replied" {{ request('status') == 'replied' ? 'selected' : '' }}>Replied</option>
                </select>
                <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-md hover:bg-indigo-700">
                    Filter
                </button>
            </form>
        </div>
        @if($newMessagesCount > 0)
            <div class="bg-red-100 text-red-800 px-4 py-2 rounded-md font-semibold">
                {{ $newMessagesCount }} New Messages
            </div>
        @endif
    </div>
</div>

@if($messages->count() > 0)
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($messages as $message)
                    <tr class="hover:bg-gray-50 {{ $message->status === 'new' ? 'bg-blue-50' : '' }}">
                        <td class="px-6 py-4">
                            <div class="font-semibold text-gray-900">{{ $message->name }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $message->email }}</td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">{{ Str::limit($message->subject, 50) }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $message->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                @if($message->status === 'new') bg-blue-100 text-blue-800
                                @elseif($message->status === 'read') bg-yellow-100 text-yellow-800
                                @else bg-green-100 text-green-800
                                @endif">
                                {{ ucfirst($message->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <a href="{{ route('admin.contacts.show', $message->id) }}" class="text-indigo-600 hover:text-indigo-900 font-semibold">
                                View
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $messages->links() }}
    </div>
@else
    <div class="bg-white rounded-lg shadow-md p-12 text-center">
        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
        </svg>
        <h3 class="text-xl font-semibold text-gray-900 mb-2">No Messages Found</h3>
        <p class="text-gray-600">No contact messages matching your filters.</p>
    </div>
@endif
@endsection