<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Section;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    /**
     * Show form for creating a new section.
     */
    public function create(Course $course)
    {
        //$this->authorize('update', $course);

        return view('sections.create', compact('course'));
    }

    /**
     * Store a newly created section.
     */
    public function store(Request $request, Course $course)
    {
        //$this->authorize('update', $course);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'order' => 'required|integer|min:0',
        ]);

        $validated['course_id'] = $course->id;

        $section = Section::create($validated);

        return redirect()
            ->route('courses.show', $course)
            ->with('success', 'Section created successfully!');
    }

    /**
     * Show form for editing section.
     */
    public function edit(Section $section)
    {
        //$this->authorize('update', $section->course);

        return view('sections.edit', compact('section'));
    }

    /**
     * Update the specified section.
     */
    public function update(Request $request, Section $section)
    {
        //$this->authorize('update', $section->course);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'order' => 'required|integer|min:0',
        ]);

        $section->update($validated);

        return redirect()
            ->route('courses.show', $section->course)
            ->with('success', 'Section updated successfully!');
    }

    /**
     * Remove the specified section.
     */
    public function destroy(Section $section)
    {
        //$this->authorize('update', $section->course);

        $course = $section->course;
        $section->delete();

        return redirect()
            ->route('courses.show', $course)
            ->with('success', 'Section deleted successfully!');
    }

    /**
     * Reorder sections.
     */
    public function reorder(Request $request, Course $course)
    {
        //$this->authorize('update', $course);

        $validated = $request->validate([
            'sections' => 'required|array',
            'sections.*.id' => 'required|exists:sections,id',
            'sections.*.order' => 'required|integer|min:0',
        ]);

        foreach ($validated['sections'] as $sectionData) {
            Section::where('id', $sectionData['id'])
                ->update(['order' => $sectionData['order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Sections reordered successfully!',
        ]);
    }
}