<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLessonRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $lesson = $this->route('lesson');
        $course = $lesson->section->course;
        
        return $this->user()->isAdmin() || 
               ($this->user()->isTeacher() && $course->teacher_id === $this->user()->id);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'type' => ['required', 'in:video,text,quiz,assignment'],
            'video_url' => ['nullable', 'url', 'required_if:type,video'],
            'duration_minutes' => ['nullable', 'integer', 'min:1', 'max:600'],
            'order' => ['required', 'integer', 'min:0'],
            'is_preview' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Please provide a lesson title.',
            'title.max' => 'The lesson title is too long.',
            'type.required' => 'Please select a lesson type.',
            'type.in' => 'The selected lesson type is invalid.',
            'video_url.url' => 'Please provide a valid video URL.',
            'video_url.required_if' => 'Video URL is required for video lessons.',
            'duration_minutes.integer' => 'Duration must be a number.',
            'duration_minutes.min' => 'Duration must be at least 1 minute.',
            'duration_minutes.max' => 'Duration cannot exceed 600 minutes (10 hours).',
            'order.required' => 'Please specify the lesson order.',
            'order.integer' => 'The order must be a number.',
            'order.min' => 'The order must be at least 0.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'video_url' => 'video URL',
            'duration_minutes' => 'duration',
            'is_preview' => 'preview status',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('is_preview')) {
            $this->merge([
                'is_preview' => $this->boolean('is_preview'),
            ]);
        }
    }
}