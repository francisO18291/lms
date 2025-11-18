<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class TeacherController extends Controller
{
    /**
     * Display a listing of teachers (Admin only).
     */
    public function index(Request $request)
    {
        //$this->authorize('viewAny', User::class);

        $query = User::where('role_id', 2) // Teachers only
            ->withCount('taughtCourses');

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $teachers = $query->paginate(20);

        return view('teachers.index', compact('teachers'));
    }

    /**
     * Show teacher profile with their courses.
     */
    public function show(User $user)
{
    $user->load('role');
    if (!$user->isTeacher()) {
        abort(404);
    }

    $courses = $user->taughtCourses()
        ->withCount('enrollments')
        ->latest()
        ->get();

    $stats = [
        'total_courses' => $courses->count(),
        'published_courses' => $courses->where('status', 'published')->count(),
        'total_students' => $courses->sum('enrollments_count'),
        'total_revenue' => $user->taughtCourses()
            ->join('enrollments', 'courses.id', '=', 'enrollments.course_id')
            ->sum('enrollments.price_paid'),
    ];

    return view('teachers.show', compact('user', 'courses', 'stats'));
}

    /**
     * Show form for creating a new teacher (Admin only).
     */
    public function create()
    {
        //$this->authorize('create', User::class);

        return view('teachers.create');
    }

    /**
     * Store a newly created teacher.
     */
    public function store(Request $request)
    {
        //$this->authorize('create', User::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['role_id'] = 2; // Teacher role
        $validated['is_active'] = true;

        $teacher = User::create($validated);

        return redirect()
            ->route('teachers.index')
            ->with('success', 'Teacher created successfully!');
    }

    /**
     * Show form for editing teacher.
     */
    public function edit(User $user)
{
    //$this->authorize('update', $user);
    $user->load('role');

    if (!$user->isTeacher()) {
        abort(404);
    }

    return view('teachers.edit', compact('user'));
}
    /**
     * Update the specified teacher.
     */
    public function update(Request $request, User $user)
{
    //$this->authorize('update', $user);

    if (!$user->isTeacher()) {
        abort(404);
    }

    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
        'phone' => 'nullable|string|max:20',
        'bio' => 'nullable|string',
        'is_active' => 'required|boolean',
        'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
    ]);

    // Handle avatar upload
    if ($request->hasFile('avatar')) {
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }
        $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
    }

    // Handle password if provided
    if ($request->filled('password')) {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);
        $validated['password'] = Hash::make($request->password);
    }

    $user->update($validated);

    return redirect()
        ->route('teachers.show', $user)
        ->with('success', 'Teacher updated successfully!');
}


    /**
     * Remove the specified teacher.
     */
    public function destroy(User $user)
    {
        //$this->authorize('delete', $user);

        if (!$user->isTeacher()) {
            abort(404);
        }

        // Check if teacher has courses
        if ($user->taughtCourses()->count() > 0) {
            return back()->with('error', 'Cannot delete teacher with existing courses.');
        }

        $user->delete();

        return redirect()
            ->route('teachers.index')
            ->with('success', 'Teacher deleted successfully!');
    }
}