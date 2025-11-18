<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Section;
use App\Models\LessonProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class LessonController extends Controller
{
    /**
     * Display lesson for learning.
     */
    public function show(Course $course, Lesson $lesson)
    {
        $user = Auth::user();

        // Check if user is enrolled or lesson is preview
        if (!$lesson->canPreview() && !$user->isEnrolledIn($course)) {
            return redirect()
                ->route('courses.show', $course)
                ->with('error', 'You must be enrolled to view this lesson.');
        }

        $course->load(['sections.lessons']);
        
        // Get or create progress for this lesson
        $progress = null;
        if ($user->isEnrolledIn($course)) {
            $progress = LessonProgress::getOrCreateProgress($user->id, $lesson->id);
        }

        // Get next lesson
        $nextLesson = $this->getNextLesson($lesson);

        return view('lessons.show', compact('course', 'lesson', 'progress', 'nextLesson'));
    }

    /**
     * Show form for creating a new lesson.
     */
    public function create(Section $section)
    {
        //$this->authorize('create', [Lesson::class, $section->course]);

        return view('lessons.create', compact('section'));
    }

    /**
     * Store a newly created lesson.
     */
    public function store(Request $request, Section $section)
    {
        //$this->authorize('create', [Lesson::class, $section->course]);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'type' => 'required|in:video,text,quiz,assignment',
            'video_url' => 'nullable|url',
            'duration_minutes' => 'nullable|integer|min:1',
            'order' => 'required|integer|min:0',
            'is_preview' => 'boolean',
        ]);

        $validated['section_id'] = $section->id;
        $validated['slug'] = Str::slug($validated['title']);

        $lesson = Lesson::create($validated);

        return redirect()
            ->route('courses.show', $section->course)
            ->with('success', 'Lesson created successfully!');
    }

    /**
     * Show form for editing lesson.
     */
    public function edit(Lesson $lesson)
    {
        $course = $lesson->section->course;
        //$this->authorize('update', [$lesson, $course]);

        return view('lessons.edit', compact('lesson'));
    }

    /**
     * Update the specified lesson.
     */
    public function update(Request $request, Lesson $lesson)
    {
        $course = $lesson->section->course;
        //$this->authorize('update', [$lesson, $course]);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'type' => 'required|in:video,text,quiz,assignment',
            'video_url' => 'nullable|url',
            'duration_minutes' => 'nullable|integer|min:1',
            'order' => 'required|integer|min:0',
            'is_preview' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['title']);

        $lesson->update($validated);

        return redirect()
            ->route('courses.show', $course)
            ->with('success', 'Lesson updated successfully!');
    }

    /**
     * Remove the specified lesson.
     */
    public function destroy(Lesson $lesson)
    {
        $course = $lesson->section->course;
        //$this->authorize('delete', [$lesson, $course]);

        $lesson->delete();

        return redirect()
            ->route('courses.show', $course)
            ->with('success', 'Lesson deleted successfully!');
    }

    /**
     * Mark lesson as complete.
     */
    public function complete(Lesson $lesson)
    {
        $user = Auth::user();
        $course = $lesson->section->course;

        if (!$user->isEnrolledIn($course)) {
            return response()->json(['error' => 'Not enrolled in course'], 403);
        }

        $progress = LessonProgress::getOrCreateProgress($user->id, $lesson->id);
        $progress->markAsCompleted();

        return response()->json([
            'success' => true,
            'message' => 'Lesson marked as complete!',
            'progress' => $progress,
        ]);
    }

    /**
     * Mark lesson as incomplete.
     */
    public function incomplete(Lesson $lesson)
    {
        $user = Auth::user();
        $course = $lesson->section->course;

        if (!$user->isEnrolledIn($course)) {
            return response()->json(['error' => 'Not enrolled in course'], 403);
        }

        $progress = LessonProgress::where('user_id', $user->id)
            ->where('lesson_id', $lesson->id)
            ->first();

        if ($progress) {
            $progress->markAsIncomplete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Lesson marked as incomplete!',
        ]);
    }

    /**
     * Get the next lesson in the course.
     */
    protected function getNextLesson(Lesson $currentLesson)
    {
        $section = $currentLesson->section;
        $course = $section->course;

        // Try to find next lesson in the same section
        $nextInSection = Lesson::where('section_id', $section->id)
            ->where('order', '>', $currentLesson->order)
            ->orderBy('order')
            ->first();

        if ($nextInSection) {
            return $nextInSection;
        }

        // Try to find first lesson in next section
        $nextSection = Section::where('course_id', $course->id)
            ->where('order', '>', $section->order)
            ->orderBy('order')
            ->first();

        if ($nextSection) {
            return $nextSection->lessons()->orderBy('order')->first();
        }

        return null;
    }
}