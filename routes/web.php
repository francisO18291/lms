<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\PolicyController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\Admin\AdminContactController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
})->name('home');



/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    
    // Dashboard - Role-based routing
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile Management
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Course Management Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:admin,teacher'])->group(function () {
    Route::get('/courses/create', [CourseController::class, 'create'])->name('courses.create');
    Route::post('/courses', [CourseController::class, 'store'])->name('courses.store');
    Route::get('/courses/{course:slug}/edit', [CourseController::class, 'edit'])->name('courses.edit');
    Route::put('/courses/{course:slug}', [CourseController::class, 'update'])->name('courses.update');
    Route::delete('/courses/{course:slug}', [CourseController::class, 'destroy'])->name('courses.destroy');

    });
    
    // Additional course actions
    Route::post('/courses/{course:slug}/publish', [CourseController::class, 'publish'])->name('courses.publish');
    Route::post('/courses/{course:slug}/unpublish', [CourseController::class, 'unpublish'])->name('courses.unpublish');

    /*
    |--------------------------------------------------------------------------
    | Section Management Routes
    |--------------------------------------------------------------------------
    */
    Route::get('/courses/{course:slug}/sections/create', [SectionController::class, 'create'])->name('sections.create');
    Route::post('/courses/{course:slug}/sections', [SectionController::class, 'store'])->name('sections.store');
    Route::get('/sections/{section}/edit', [SectionController::class, 'edit'])->name('sections.edit');
    Route::put('/sections/{section}', [SectionController::class, 'update'])->name('sections.update');
    Route::delete('/sections/{section}', [SectionController::class, 'destroy'])->name('sections.destroy');
    Route::post('/courses/{course:slug}/sections/reorder', [SectionController::class, 'reorder'])->name('sections.reorder');

    /*
    |--------------------------------------------------------------------------
    | Lesson Management Routes
    |--------------------------------------------------------------------------
    */
    Route::get('/sections/{section}/lessons/create', [LessonController::class, 'create'])->name('lessons.create');
    Route::post('/sections/{section}/lessons', [LessonController::class, 'store'])->name('lessons.store');
    Route::get('/lessons/{lesson}/edit', [LessonController::class, 'edit'])->name('lessons.edit');
    Route::put('/lessons/{lesson}', [LessonController::class, 'update'])->name('lessons.update');
    Route::delete('/lessons/{lesson}', [LessonController::class, 'destroy'])->name('lessons.destroy');
    
    // Lesson viewing and progress
    Route::get('/courses/{course:slug}/lessons/{lesson:slug}', [LessonController::class, 'show'])
        ->name('courses.lessons.show');
    Route::post('/lessons/{lesson}/complete', [LessonController::class, 'complete'])
        ->name('lessons.complete');
    Route::post('/lessons/{lesson}/incomplete', [LessonController::class, 'incomplete'])
        ->name('lessons.incomplete');
    /*
    |--------------------------------------------------------------------------
    | Enrollment Routes
    |--------------------------------------------------------------------------
    */
    Route::get('/enrollments', [EnrollmentController::class, 'index'])->name('enrollments.index');
    Route::post('/enrollments/courses/{course:slug}', [EnrollmentController::class, 'store'])->name('enrollments.store');
    Route::get('/enrollments/{enrollment}', [EnrollmentController::class, 'show'])->name('enrollments.show');
    Route::delete('/enrollments/{enrollment}', [EnrollmentController::class, 'destroy'])->name('enrollments.destroy');
    Route::get('/enrollments/{enrollment}/certificate', [EnrollmentController::class, 'certificate'])->name('enrollments.certificate');

    /*
    |--------------------------------------------------------------------------
    | Student Management Routes (Admin & Teacher)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:admin,teacher'])->group(function () {
        Route::get('/students', [StudentController::class, 'index'])->name('students.index');
        Route::get('/students/{user}', [StudentController::class, 'show'])->name('students.show');
        Route::get('/students/{user}/courses/{course}', [StudentController::class, 'courseProgress'])->name('students.course-progress');
        Route::patch('/students/{user}/status', [StudentController::class, 'updateStatus'])->name('students.update-status')->middleware('role:admin');
    });

    /*
    |--------------------------------------------------------------------------
    | Teacher Management Routes (Admin Only)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/teachers', [TeacherController::class, 'index'])->name('teachers.index');
        Route::get('/teachers/create', [TeacherController::class, 'create'])->name('teachers.create');
        Route::post('/teachers', [TeacherController::class, 'store'])->name('teachers.store');
        Route::get('/teachers/{user}', [TeacherController::class, 'show'])->name('teachers.show');
        Route::get('/teachers/{user}/edit', [TeacherController::class, 'edit'])->name('teachers.edit');
        Route::put('/teachers/{user}', [TeacherController::class, 'update'])->name('teachers.update');
        Route::delete('/teachers/{user}', [TeacherController::class, 'destroy'])->name('teachers.destroy');
    });

    Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/contacts', [AdminContactController::class, 'index'])->name('contacts.index');
    Route::get('/contacts/{id}', [AdminContactController::class, 'show'])->name('contacts.show');
    Route::post('/contacts/{id}/reply', [AdminContactController::class, 'reply'])->name('contacts.reply');
    Route::delete('/contacts/{id}', [AdminContactController::class, 'destroy'])->name('contacts.destroy');
    Route::patch('/contacts/{id}/mark-read', [AdminContactController::class, 'markAsRead'])->name('contacts.mark-read');
    });

    /*
    |--------------------------------------------------------------------------
    | Category Management Routes (Admin Only)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::get('/categories/{category:slug}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::put('/categories/{category:slug}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category:slug}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    });

});


/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';

// Public course browsing
Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
Route::get('/courses/{course:slug}', [CourseController::class, 'show'])->name('courses.show');

// Public category browsing
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/{category:slug}', [CategoryController::class, 'show'])->name('categories.show');

Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');


Route::get('/privacy-policy', [PolicyController::class, 'privacyPolicy'])->name('privacy.policy');
Route::get('/terms-of-service', [PolicyController::class, 'termsOfService'])->name('terms.service');
Route::get('/cookie-policy', [PolicyController::class, 'cookiePolicy'])->name('cookie.policy');
