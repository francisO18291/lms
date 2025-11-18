<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EnrollmentResource;
use App\Http\Resources\EnrollmentCollection;
use App\Models\Course;
use App\Models\Enrollment;
use App\Notifications\EnrollmentConfirmation;
use App\Notifications\NewStudentEnrolledNotification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class EnrollmentController extends Controller
{
    /**
     * Display a listing of user's enrollments.
     */
    public function index(Request $request): EnrollmentCollection
    {
        $enrollments = $request->user()
            ->enrollments()
            ->with(['course.teacher', 'course.category'])
            ->latest()
            ->paginate($request->get('per_page', 15));

        return new EnrollmentCollection($enrollments);
    }

    /**
     * Enroll user in a course.
     */
    public function store(Request $request, Course $course): JsonResponse
    {
        $user = $request->user();

        // Validate
        if ($user->isEnrolledIn($course)) {
            return response()->json([
                'message' => 'You are already enrolled in this course.',
            ], 422);
        }

        if (!$course->isPublished()) {
            return response()->json([
                'message' => 'This course is not available for enrollment.',
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Create enrollment
            $enrollment = Enrollment::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'price_paid' => $course->price,
                'progress' => 0,
            ]);

            // Send notifications
            $user->notify(new EnrollmentConfirmation($enrollment));
            $course->teacher->notify(new NewStudentEnrolledNotification($enrollment));

            DB::commit();

            return response()->json([
                'message' => 'Successfully enrolled in the course!',
                'data' => new EnrollmentResource($enrollment->load(['course', 'user'])),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'message' => 'Failed to enroll in course. Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified enrollment.
     */
    public function show(Request $request, Enrollment $enrollment): EnrollmentResource
    {
        $this->authorize('view', $enrollment);

        $enrollment->load(['course.sections.lessons', 'user']);

        return new EnrollmentResource($enrollment);
    }

    /**
     * Cancel enrollment.
     */
    public function destroy(Request $request, Enrollment $enrollment): JsonResponse
    {
        $this->authorize('delete', $enrollment);

        if ($enrollment->progress >= 10) {
            return response()->json([
                'message' => 'Cannot cancel enrollment after 10% progress.',
            ], 422);
        }

        $enrollment->delete();

        return response()->json([
            'message' => 'Enrollment cancelled successfully.',
        ], 200);
    }
}