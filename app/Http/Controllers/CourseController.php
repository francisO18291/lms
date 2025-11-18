<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    /**
     * Display a listing of courses.
     */
    public function index(Request $request)
    {
        $query = Course::with(['teacher', 'category'])
            ->published();

        // Filter by category
        if ($request->has('category')) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Filter by level
        if ($request->has('level')) {
            $query->where('level', $request->level);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sort
        $sortBy = $request->get('sort', 'latest');
        switch ($sortBy) {
            case 'popular':
                $query->withCount('enrollments')->orderBy('enrollments_count', 'desc');
                break;
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            default:
                $query->latest();
        }

        $courses = $query->paginate(12);
        $categories = Category::withCount('courses')->get();

        return view('courses.index', compact('courses', 'categories'));
    }

    /**
     * Show the form for creating a new course.
     */
    public function create()
    {
        //$this->authorize('create', Course::class);
        
        $categories = Category::all();
        return view('courses.create', compact('categories'));
        //return view('courses.create');
    }

    /**
     * Store a newly created course.
     */
    public function store(Request $request)
    {
        //$this->authorize('create', Course::class);

        $validated = $request->validate([
            'title' => 'required|string|max:255|unique:courses',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'requirements' => 'nullable|string',
            'learning_outcomes' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'level' => 'required|in:beginner,intermediate,advanced',
            'duration_hours' => 'nullable|integer|min:1',
            'thumbnail' => 'nullable|image|max:2048',
        ]);

        $validated['teacher_id'] = Auth::id();
        $validated['slug'] = Str::slug($validated['title']);
        $validated['status'] = 'draft';

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        $course = Course::create($validated);

        return redirect()
            ->route('courses.show', $course)
            ->with('success', 'Course created successfully!');
    }

    /**
     * Display the specified course.
     */
    public function show(Course $course)
    {
        $course->load(['teacher', 'category', 'sections.lessons']);

        $isEnrolled = Auth::check() 
            ? Auth::user()->isEnrolledIn($course) 
            : false;

        $enrollment = $isEnrolled 
            ? Auth::user()->enrollments()->where('course_id', $course->id)->first()
            : null;

        return view('courses.show', compact('course', 'isEnrolled', 'enrollment'));
    }

    /**
     * Show the form for editing the course.
     */
    public function edit(Course $course)
    {
        //$this->authorize('update', $course);

        $categories = Category::all();
        return view('courses.edit', compact('course', 'categories'));
    }

    /**
     * Update the specified course.
     */
    public function update(Request $request, Course $course)
    {
        //$this->authorize('update', $course);

        $validated = $request->validate([
            'title' => 'required|string|max:255|unique:courses,title,' . $course->id,
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'requirements' => 'nullable|string',
            'learning_outcomes' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'level' => 'required|in:beginner,intermediate,advanced',
            'duration_hours' => 'nullable|integer|min:1',
            'thumbnail' => 'nullable|image|max:2048',
            'status' => 'required|in:draft,published,archived',
        ]);

        $validated['slug'] = Str::slug($validated['title']);

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        $course->update($validated);

        return redirect()
            ->route('courses.show', $course)
            ->with('success', 'Course updated successfully!');
    }

    /**
     * Remove the specified course.
     */
    public function destroy(Course $course)
    {
        //$this->authorize('delete', $course);

        $course->delete();

        return redirect()
            ->route('dashboard')
            ->with('success', 'Course deleted successfully!');
    }

    /**
     * Publish the course.
     */
    public function publish(Course $course)
    {
        //$this->authorize('update', $course);

        $course->update(['status' => 'published']);

        return back()->with('success', 'Course published successfully!');
    }

    /**
     * Unpublish the course.
     */
    public function unpublish(Course $course)
    {
        //$this->authorize('update', $course);

        $course->update(['status' => 'draft']);

        return back()->with('success', 'Course unpublished successfully!');
    }
}