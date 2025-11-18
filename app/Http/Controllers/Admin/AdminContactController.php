<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\ContactMessage;
use App\Mail\ContactMessageReply;
#use App\Http\Controllers\Admin\Mail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;


class AdminContactController extends Controller
{
    public function index(Request $request)
    {
        $query = ContactMessage::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        $messages = $query->latest()->paginate(15);
        $newMessagesCount = ContactMessage::where('status', 'new')->count();

        return view('admin.contacts.index', compact('messages', 'newMessagesCount'));
    }

    public function show($id)
    {
        $message = ContactMessage::findOrFail($id);

        // Mark as read if it's new
        if ($message->status === 'new') {
            $message->update(['status' => 'read']);
        }

        return view('admin.contacts.show', compact('message'));
    }

    public function reply(Request $request, $id)
    {
        $request->validate([
            'reply' => 'required|string|max:2000',
        ]);

        $message = ContactMessage::findOrFail($id);

        $message->update([
            'status' => 'replied',
            'admin_reply' => $request->reply,
            'replied_at' => now(),
        ]);

        // Email notification removed for now - reply is stored in database
        // Admin should copy the reply and send manually via email
         try {
        Mail::to($message->email)->send(new ContactMessageReply($message));
        
        \Log::info('Reply email sent', [
            'to' => $message->email,
            'message_id' => $message->id
        ]);
        
        return back()->with('success', 'Reply sent successfully! An email has been sent to the customer.');
    } catch (\Exception $e) {
        \Log::error('Failed to send reply email', [
            'error' => $e->getMessage(),
            'message_id' => $message->id
        ]);

        return back()->with('success', 'Reply saved to database, but email failed to send. You may want to send it manually.');
    }
    }

    public function destroy($id)
    {
        $message = ContactMessage::findOrFail($id);
        $message->delete();

        return redirect()->route('admin.contacts.index')
            ->with('success', 'Message deleted successfully.');
    }

    public function markAsRead($id)
    {
        $message = ContactMessage::findOrFail($id);
        $message->update(['status' => 'read']);

        return back()->with('success', 'Message marked as read.');
    }
}