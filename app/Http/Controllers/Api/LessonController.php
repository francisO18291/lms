<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LessonProgressResource;
use App\Models\Lesson;
use App\Models\LessonProgress;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class LessonController extends Controller
{
    /**
     * Mark lesson as complete.
     */
    public function complete(Request $request, Lesson $lesson): JsonResponse
    {
        $user = $request->user();
        $course = $lesson->section->course;

        // Check enrollment
        if (!$user->isEnrolledIn($course)) {
            return response()->json([
                'message' => 'You must be enrolled in the course to mark lessons as complete.',
            ], 403);
        }

        // Get or create progress
        $progress = LessonProgress::getOrCreateProgress($user->id, $lesson->id);
        $progress->markAsCompleted();

        return response()->json([
            'message' => 'Lesson marked as complete!',
            'data' => new LessonProgressResource($progress->load(['lesson', 'user'])),
        ], 200);
    }

    /**
     * Mark lesson as incomplete.
     */
    public function incomplete(Request $request, Lesson $lesson): JsonResponse
    {
        $user = $request->user();
        $course = $lesson->section->course;

        // Check enrollment
        if (!$user->isEnrolledIn($course)) {
            return response()->json([
                'message' => 'You must be enrolled in the course.',
            ], 403);
        }

        // Find progress
        $progress = LessonProgress::where('user_id', $user->id)
            ->where('lesson_id', $lesson->id)
            ->first();

        if (!$progress) {
            return response()->json([
                'message' => 'Lesson progress not found.',
            ], 404);
        }

        $progress->markAsIncomplete();

        return response()->json([
            'message' => 'Lesson marked as incomplete!',
            'data' => new LessonProgressResource($progress->load(['lesson', 'user'])),
        ], 200);
    }

    /**
     * Get lesson progress for authenticated user.
     */
    public function progress(Request $request, Lesson $lesson): JsonResponse
    {
        $user = $request->user();
        $course = $lesson->section->course;

        // Check enrollment
        if (!$user->isEnrolledIn($course)) {
            return response()->json([
                'message' => 'You must be enrolled in the course.',
            ], 403);
        }

        // Get progress
        $progress = LessonProgress::where('user_id', $user->id)
            ->where('lesson_id', $lesson->id)
            ->with(['lesson', 'user'])
            ->first();

        if (!$progress) {
            return response()->json([
                'message' => 'Lesson not started yet.',
                'data' => [
                    'is_completed' => false,
                    'status' => 'not_started',
                ],
            ], 200);
        }

        return response()->json([
            'data' => new LessonProgressResource($progress),
        ], 200);
    }
}