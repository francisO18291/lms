<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCourseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->isTeacher() || $this->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255', 'unique:courses,title'],
            'category_id' => ['required', 'exists:categories,id'],
            'description' => ['required', 'string', 'min:50'],
            'requirements' => ['nullable', 'string', 'max:1000'],
            'learning_outcomes' => ['nullable', 'string', 'max:1000'],
            'price' => ['required', 'numeric', 'min:0', 'max:99999.99'],
            'level' => ['required', 'in:beginner,intermediate,advanced'],
            'duration_hours' => ['nullable', 'integer', 'min:1', 'max:1000'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'is_featured' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Please provide a course title.',
            'title.unique' => 'A course with this title already exists.',
            'category_id.required' => 'Please select a category.',
            'category_id.exists' => 'The selected category is invalid.',
            'description.required' => 'Please provide a course description.',
            'description.min' => 'The course description must be at least 50 characters.',
            'price.required' => 'Please set a price for the course.',
            'price.min' => 'The price cannot be negative.',
            'price.max' => 'The price is too high.',
            'level.required' => 'Please select a difficulty level.',
            'level.in' => 'The selected level is invalid.',
            'thumbnail.image' => 'The thumbnail must be an image file.',
            'thumbnail.mimes' => 'The thumbnail must be a JPEG, PNG, JPG, or WebP file.',
            'thumbnail.max' => 'The thumbnail size must not exceed 2MB.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'category_id' => 'category',
            'learning_outcomes' => 'learning outcomes',
            'duration_hours' => 'duration',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('is_featured')) {
            $this->merge([
                'is_featured' => $this->boolean('is_featured'),
            ]);
        }
    }
}