@extends('layouts.dashboard')

@section('title', 'Contact Message')
@section('page-title', 'Contact Message Details')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.contacts.index') }}" class="text-indigo-600 hover:text-indigo-700">
        ← Back to Messages
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Content -->
    <div class="lg:col-span-2">
        <!-- Message Details -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ $message->subject }}</h2>
                    <p class="text-gray-600">Received {{ $message->created_at->format('M d, Y \a\t h:i A') }}</p>
                </div>
                <span class="px-4 py-2 text-sm font-semibold rounded-full
                    @if($message->status === 'new') bg-blue-100 text-blue-800
                    @elseif($message->status === 'read') bg-yellow-100 text-yellow-800
                    @else bg-green-100 text-green-800
                    @endif">
                    {{ ucfirst($message->status) }}
                </span>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-600 mb-2">Message</label>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-gray-700 whitespace-pre-wrap">{{ $message->message }}</p>
                </div>
            </div>

            @if($message->admin_reply)
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-600 mb-2">Your Reply</label>
                    <div class="bg-green-50 p-4 rounded-lg">
                        <p class="text-gray-700 whitespace-pre-wrap">{{ $message->admin_reply }}</p>
                        <p class="text-sm text-gray-500 mt-2">Replied on {{ $message->replied_at->format('M d, Y \a\t h:i A') }}</p>
                    </div>
                </div>
            @endif

            <!-- Reply Form -->
            @if($message->status !== 'replied')
                <div class="pt-6 border-t border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Send Reply</h3>
                    <form action="{{ route('admin.contacts.reply', $message->id) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Your Reply *</label>
                            <textarea name="reply" rows="6" required class="w-full px-4 py-2 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Type your reply here..."></textarea>
                            @error('reply')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="submit" class="bg-indigo-600 text-white px-6 py-3 rounded-md hover:bg-indigo-700 font-semibold">
                            Send Reply
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>

    <!-- Sidebar -->
    <div class="lg:col-span-1">
        <!-- Sender Info -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Sender Information</h3>
            <div class="space-y-3 text-sm">
                <div>
                    <span class="text-gray-600">Name:</span>
                    <p class="font-semibold text-gray-900">{{ $message->name }}</p>
                </div>
                <div>
                    <span class="text-gray-600">Email:</span>
                    <p class="font-semibold text-gray-900">{{ $message->email }}</p>
                </div>
                <div>
                    <span class="text-gray-600">Date:</span>
                    <p class="font-semibold text-gray-900">{{ $message->created_at->format('M d, Y h:i A') }}</p>
                </div>
                <div>
                    <span class="text-gray-600">Status:</span>
                    <p class="font-semibold text-gray-900">{{ ucfirst($message->status) }}</p>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions</h3>
            <div class="space-y-2">
                <a href="mailto:{{ $message->email }}" class="block w-full bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 text-center font-semibold">
                    Email Sender
                </a>
                @if($message->status === 'new')
                    <form action="{{ route('admin.contacts.mark-read', $message->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="w-full bg-yellow-600 text-white px-4 py-2 rounded-md hover:bg-yellow-700 font-semibold">
                            Mark as Read
                        </button>
                    </form>
                @endif
                <form action="{{ route('admin.contacts.destroy', $message->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this message?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 font-semibold">
                        Delete Message
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection