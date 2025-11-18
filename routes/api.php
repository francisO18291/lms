<?php

use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\EnrollmentController;
use App\Http\Controllers\Api\LessonController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->group(function () {
    
    // User info
    Route::get('/user', function (Request $request) {
        return $request->user()->load('role');
    });

    /*
    |--------------------------------------------------------------------------
    | Course API Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('courses')->name('api.courses.')->group(function () {
        Route::get('/', [CourseController::class, 'index'])->name('index');
        Route::get('/{course}', [CourseController::class, 'show'])->name('show');
        Route::post('/', [CourseController::class, 'store'])->name('store');
        Route::put('/{course}', [CourseController::class, 'update'])->name('update');
        Route::delete('/{course}', [CourseController::class, 'destroy'])->name('destroy');
        
        // Course sections and lessons
        Route::get('/{course}/curriculum', [CourseController::class, 'curriculum'])->name('curriculum');
    });

    /*
    |--------------------------------------------------------------------------
    | Enrollment API Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('enrollments')->name('api.enrollments.')->group(function () {
        Route::get('/', [EnrollmentController::class, 'index'])->name('index');
        Route::post('/courses/{course}', [EnrollmentController::class, 'store'])->name('store');
        Route::get('/{enrollment}', [EnrollmentController::class, 'show'])->name('show');
        Route::delete('/{enrollment}', [EnrollmentController::class, 'destroy'])->name('destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Lesson Progress API Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('lessons')->name('api.lessons.')->group(function () {
        Route::post('/{lesson}/complete', [LessonController::class, 'complete'])->name('complete');
        Route::post('/{lesson}/incomplete', [LessonController::class, 'incomplete'])->name('incomplete');
        Route::get('/{lesson}/progress', [LessonController::class, 'progress'])->name('progress');
    });

    /*
    |--------------------------------------------------------------------------
    | Statistics API Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('stats')->name('api.stats.')->group(function () {
        Route::get('/dashboard', function (Request $request) {
            $user = $request->user();
            
            if ($user->isStudent()) {
                return response()->json([
                    'enrolled_courses' => $user->enrollments()->count(),
                    'completed_courses' => $user->enrollments()->completed()->count(),
                    'in_progress_courses' => $user->enrollments()->inProgress()->count(),
                    'completed_lessons' => $user->lessonProgress()->completed()->count(),
                ]);
            } elseif ($user->isTeacher()) {
                return response()->json([
                    'total_courses' => $user->taughtCourses()->count(),
                    'published_courses' => $user->taughtCourses()->published()->count(),
                    'total_students' => $user->taughtCourses()
                        ->join('enrollments', 'courses.id', '=', 'enrollments.course_id')
                        ->distinct('enrollments.user_id')
                        ->count('enrollments.user_id'),
                ]);
            }
            
            return response()->json(['error' => 'Invalid role'], 403);
        })->name('dashboard');
    });
});

/*
|--------------------------------------------------------------------------
| Public API Routes
|--------------------------------------------------------------------------
*/
Route::prefix('public')->name('api.public.')->group(function () {
    Route::get('/courses', [CourseController::class, 'publicIndex'])->name('courses.index');
    Route::get('/courses/{course}', [CourseController::class, 'publicShow'])->name('courses.show');
    Route::get('/categories', function () {
        return \App\Models\Category::withCount('courses')->get();
    })->name('categories.index');
});